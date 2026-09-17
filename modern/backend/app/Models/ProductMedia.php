<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMedia extends Model
{
    protected $fillable = ['path', 'original_name', 'mime', 'width', 'height', 'bytes', 'uploaded_by'];

    protected $hidden = ['path', 'uploaded_by'];

    protected $appends = ['url'];

    public function getUrlAttribute(): string
    {
        return route('api.v1.catalog.media', $this->id);
    }
}
