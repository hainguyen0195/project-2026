<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\CmsUserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request): CmsUserResource
    {
        if (! Auth::guard('web')->attempt([...$request->validated(), 'is_active' => true])) {
            throw ValidationException::withMessages(['email' => 'Email hoặc mật khẩu không chính xác.']);
        }
        $user = Auth::guard('web')->user();
        if (! $user->role) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            throw ValidationException::withMessages(['email' => 'Email hoặc mật khẩu không chính xác.']);
        }
        $request->session()->regenerate();
        $request->session()->put('cms_auth_version', $user->auth_version);
        $user->last_login_at = now();
        $user->save();

        return new CmsUserResource($user);
    }

    public function logout(Request $request): Response
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }

    public function password(ChangePasswordRequest $request): CmsUserResource
    {
        $user = DB::transaction(function () use ($request): User {
            $user = User::lockForUpdate()->findOrFail($request->user()->id);
            abort_unless($user->is_active && $user->auth_version === $request->session()->get('cms_auth_version'), 401);
            if (! Hash::check($request->validated('current_password'), $user->password)) {
                throw ValidationException::withMessages(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
            }
            $user->password = $request->validated('password');
            $user->must_change_password = false;
            $user->auth_version++;
            $user->remember_token = null;
            $user->save();
            DB::table('sessions')->where('user_id', $user->id)->delete();

            return $user;
        });
        $request->session()->regenerate();
        $request->session()->put('cms_auth_version', $user->auth_version);

        return new CmsUserResource($user);
    }
}
