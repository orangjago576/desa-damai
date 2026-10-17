<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportantContact extends Model
{
    use HasFactory;

    protected $attributes = [
        'is_approved_for_publication' => false,
    ];

    protected $fillable = [
        'name',
        'position',
        'work_area',
        'phone_number',
        'number_type',
        'is_approved_for_publication',
        'last_verified_at',
    ];

    protected $casts = [
        'is_approved_for_publication' => 'boolean',
        'last_verified_at' => 'date',
    ];

    /**
     * Scope untuk mengambil data yang telah disetujui untuk konsumsi publik.
     */
    public function scopePublished($query)
    {
        return $query->where('is_approved_for_publication', true);
    }
}
