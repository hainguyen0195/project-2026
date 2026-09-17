<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateCmsAdmin extends Command
{
    protected $signature = 'cms:create-admin {email} {--name=Quản trị viên}';

    protected $description = 'Create a CMS system admin with a one-time temporary password';

    public function handle(): int
    {
        $email = strtolower(trim($this->argument('email')));
        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || User::where('email', $email)->exists()) {
            $this->error('Email không hợp lệ hoặc đã tồn tại. Không thay đổi tài khoản hiện có.');

            return self::FAILURE;
        }
        (new RoleSeeder)->run();
        $password = Str::password(24);
        $user = new User(['name' => $this->option('name'), 'email' => $email, 'password' => $password]);
        $user->role_id = Role::where('is_system', true)->firstOrFail()->id;
        $user->is_active = true;
        $user->must_change_password = true;
        $user->save();
        $this->info('Email: '.$email);
        $this->line('Mật khẩu tạm thời: '.$password);
        $this->warn('Bắt buộc đổi mật khẩu ở lần đăng nhập đầu. Lưu thông tin này ở nơi an toàn.');

        return self::SUCCESS;
    }
}
