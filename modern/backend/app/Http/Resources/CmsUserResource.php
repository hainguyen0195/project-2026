<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'name' => $this->name, 'email' => $this->email,
            'is_active' => $this->is_active, 'must_change_password' => $this->must_change_password,
            'role' => $this->role ? ['id' => $this->role->id, 'name' => $this->role->name, 'is_system' => $this->role->is_system] : null,
            'permissions' => $this->resource->permissions(), 'last_login_at' => $this->last_login_at?->toIso8601String(),
        ];
    }
}
