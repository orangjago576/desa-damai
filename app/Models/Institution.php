<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Institution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'abbreviation',
        'slug',
        'description',
        'logo_path',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(InstitutionMember::class, 'institution_id')->orderBy('order');
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::forget('institutions_list');
        });

        static::deleted(function ($model) {
            // Hapus file logo fisik saat record dihapus
            if ($model->logo_path && Storage::disk(config('filesystems.default_upload_disk'))->exists($model->logo_path)) {
                Storage::disk(config('filesystems.default_upload_disk'))->delete($model->logo_path);
            }
            Cache::forget('institutions_list');
        });
    }
}
