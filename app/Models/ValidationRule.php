<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ValidationRule extends Model
{
    use HasUuids;

    protected $fillable = [
        'object_type',
        'expression',
        'error_message',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForObjectType($query, $objectType)
    {
        return $query->where('object_type', $objectType);
    }
}
