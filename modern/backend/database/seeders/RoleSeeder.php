<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        if (! Role::where('is_system', true)->exists()) {
            $role = new Role(['name' => 'Quản trị hệ thống', 'permissions' => []]);
            $role->is_system = true;
            $role->save();
        }
        foreach ([
            'Biên tập viên' => ['content.view', 'content.manage', 'settings.view'],
            'Nhân viên bán hàng' => ['products.view', 'orders.view', 'orders.manage', 'customers.view', 'settings.view'],
            'Chỉ xem' => array_values(array_filter(array_keys(config('cms.permissions')), fn (string $permission): bool => str_ends_with($permission, '.view'))),
        ] as $name => $permissions) {
            Role::firstOrCreate(['name' => $name], ['permissions' => $permissions]);
        }
    }
}
