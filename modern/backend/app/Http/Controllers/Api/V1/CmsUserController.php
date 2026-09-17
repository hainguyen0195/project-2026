<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveCmsUserRequest;
use App\Http\Resources\CmsUserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CmsUserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:120'], 'page' => ['nullable', 'integer', 'min:1']]);
        $search = $filters['search'] ?? '';

        return CmsUserResource::collection(User::with('role')->when($search !== '', function ($query) use ($search): void {
            $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%');
            });
        })->orderByDesc('id')->paginate(15));
    }

    public function store(SaveCmsUserRequest $request): CmsUserResource
    {
        $user = new User;
        $user->forceFill($request->safe()->only(['name', 'email', 'password', 'role_id', 'is_active']));
        $user->must_change_password = true;
        $user->save();

        return new CmsUserResource($user->load('role'));
    }

    public function update(SaveCmsUserRequest $request, User $user): CmsUserResource
    {
        $updated = DB::transaction(function () use ($request, $user): User {
            Role::where('is_system', true)->lockForUpdate()->firstOrFail();
            $target = User::lockForUpdate()->findOrFail($user->id);
            $data = $request->safe()->only(['name', 'email', 'password', 'role_id', 'is_active']);
            if (empty($data['password'])) {
                unset($data['password']);
            }
            $newRole = Role::findOrFail($data['role_id']);
            if ($target->id === $request->user()->id && (! $data['is_active'] || ! $newRole->is_system || isset($data['password']))) {
                throw ValidationException::withMessages(['role_id' => 'Không thể tự khóa, hạ quyền hoặc đặt lại mật khẩu tại đây. Hãy dùng trang Đổi mật khẩu.']);
            }
            if ($target->isSystemAdmin() && (! $data['is_active'] || ! $newRole->is_system) && User::where('is_active', true)->whereHas('role', fn ($query) => $query->where('is_system', true))->count() <= 1) {
                throw ValidationException::withMessages(['role_id' => 'Cần giữ ít nhất một quản trị viên hệ thống đang hoạt động.']);
            }
            $target->forceFill($data);
            if ($target->isDirty(['email', 'role_id', 'is_active', 'password'])) {
                $target->auth_version++;
                $target->remember_token = null;
                if (isset($data['password'])) {
                    $target->must_change_password = true;
                }
                DB::table('sessions')->where('user_id', $target->id)->delete();
            }
            $target->save();

            return $target->fresh('role');
        });
        if ($updated->id === $request->user()->id) {
            $request->session()->regenerate();
            $request->session()->put('cms_auth_version', $updated->auth_version);
        }

        return new CmsUserResource($updated);
    }
}
