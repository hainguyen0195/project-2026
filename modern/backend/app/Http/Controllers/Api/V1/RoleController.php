<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveRoleRequest;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Role::withCount('users')->orderByDesc('is_system')->orderBy('name')->get(), 'permissions' => config('cms.permissions')]);
    }

    public function store(SaveRoleRequest $request): JsonResponse
    {
        return response()->json(['data' => Role::create($this->validatedPermissions($request))], 201);
    }

    public function update(SaveRoleRequest $request, Role $role): JsonResponse
    {
        abort_if($role->is_system, 403, 'Vai trò hệ thống được bảo vệ.');
        $role->update($this->validatedPermissions($request));

        return response()->json(['data' => $role]);
    }

    private function validatedPermissions(SaveRoleRequest $request): array
    {
        $data = $request->validated();
        foreach ($data['permissions'] as $permission) {
            if (str_ends_with($permission, '.manage') && ! in_array(str_replace('.manage', '.view', $permission), $data['permissions'], true)) {
                throw ValidationException::withMessages(['permissions' => 'Quyền quản lý phải đi kèm quyền xem cùng chức năng.']);
            }
        }

        return $data;
    }
}
