<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VillageHead extends Model
{
    use HasFactory, SoftDeletes, \App\Traits\ConvertsImagesToWebp;

    protected $fillable = [
        'name',
        'photo_path',
        'welcome_speech',
        'period_start',
        'period_end',
        'is_active',
        'order',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (VillageHead $head) {
            $head->convertToWebp(['photo_path']);
            
            if ($head->is_active) {
                // Nonaktifkan kepala desa lainnya secara otomatis (hanya satu yang aktif)
                static::where('id', '!=', $head->id ?? 0)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
