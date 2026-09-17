<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function configuration(): JsonResponse
    {
        return response()->json(['data' => config('order')]);
    }

    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(array_keys(config('order.statuses')))],
            'payment_status' => ['nullable', Rule::in(array_keys(config('order.payment_statuses')))],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('from') ? ['after_or_equal:from'] : [])],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);
        $query = Order::query();
        if (! empty($data['search'])) {
            $query->where(function ($query) use ($data): void {
                foreach (['code', 'customer_name', 'phone', 'email'] as $field) {
                    $query->orWhere($field, 'like', '%'.$data['search'].'%');
                }
            });
        }
        foreach (['status', 'payment_status'] as $field) {
            if (! empty($data[$field])) {
                $query->where($field, $data[$field]);
            }
        }
        if (! empty($data['from'])) {
            $query->whereDate('created_at', '>=', $data['from']);
        }
        if (! empty($data['to'])) {
            $query->whereDate('created_at', '<=', $data['to']);
        }

        return response()->json($query->latest('id')->paginate(20));
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json(['data' => $order]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:2000'],
            'customer_note' => ['nullable', 'string', 'max:5000'],
            'internal_note' => ['nullable', 'string', 'max:5000'],
            'payment_method' => ['required', Rule::in(array_keys(config('order.payment_methods')))],
            'shipping_fee' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'discount' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*' => ['required', 'array:name,sku,quantity,unit_price'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.sku' => ['nullable', 'string', 'max:100'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10000'],
            'items.*.unit_price' => ['required', 'integer', 'min:0', 'max:1000000000'],
        ]);
        $subtotal = array_sum(array_map(fn (array $item): int => $item['quantity'] * $item['unit_price'], $data['items']));
        if ($data['discount'] > $subtotal + $data['shipping_fee']) {
            throw ValidationException::withMessages(['discount' => 'Giảm giá không được vượt tổng đơn.']);
        }
        $order = Order::create([...$data,
            'code' => 'DH-'.strtoupper((string) Str::ulid()),
            'subtotal' => $subtotal,
            'total' => $subtotal + $data['shipping_fee'] - $data['discount'],
            'currency' => config('order.currency'),
            'status' => config('order.default_status'),
            'payment_status' => config('order.default_payment_status'),
            'created_by' => $request->user()->id,
            'history' => [['action' => 'created', 'user_id' => $request->user()->id, 'at' => now()->toIso8601String()]],
        ]);

        return response()->json(['data' => $order->fresh()], 201);
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(config('order.statuses')))],
            'payment_status' => ['required', Rule::in(array_keys(config('order.payment_statuses')))],
            'internal_note' => ['nullable', 'string', 'max:5000'],
            'version' => ['required', 'integer', 'min:1'],
        ]);
        $updated = DB::transaction(function () use ($order, $data, $request): Order {
            $current = Order::lockForUpdate()->findOrFail($order->id);
            abort_unless($current->version === $data['version'], 409, 'Đơn đã được thay đổi. Vui lòng tải lại.');
            foreach (['status' => 'transitions', 'payment_status' => 'payment_transitions'] as $field => $config) {
                if ($data[$field] !== $current->$field && ! in_array($data[$field], config('order.'.$config)[$current->$field] ?? [], true)) {
                    throw ValidationException::withMessages([$field => 'Không thể chuyển sang trạng thái này.']);
                }
            }
            $history = $current->history;
            $history[] = ['action' => 'updated', 'user_id' => $request->user()->id, 'at' => now()->toIso8601String(), 'from' => ['status' => $current->status, 'payment_status' => $current->payment_status], 'to' => ['status' => $data['status'], 'payment_status' => $data['payment_status']]];
            $current->update([...$data, 'version' => $current->version + 1, 'history' => $history]);

            return $current;
        });

        return response()->json(['data' => $updated]);
    }
}
