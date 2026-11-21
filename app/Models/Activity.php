<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Activity extends Model
{
    use HasFactory, SoftDeletes, \App\Traits\ConvertsImagesToWebp;

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Hapus file lama jika cover_image berubah
            if ($model->isDirty('cover_image')) {
                $oldCover = $model->getOriginal('cover_image');
                if ($oldCover && Storage::disk(config('filesystems.default_upload_disk'))->exists($oldCover)) {
                    Storage::disk(config('filesystems.default_upload_disk'))->delete($oldCover);
                }
            }
            // Hapus file lama jika og_image berubah
            if ($model->isDirty('og_image')) {
                $oldOg = $model->getOriginal('og_image');
                if ($oldOg && Storage::disk(config('filesystems.default_upload_disk'))->exists($oldOg)) {
                    Storage::disk(config('filesystems.default_upload_disk'))->delete($oldOg);
                }
            }

            $model->convertToWebp(['cover_image', 'og_image']);
        });

        static::saved(function () {
            Cache::forget('home_activities');
            Cache::forget('activity_categories_all');
        });

        static::deleted(function ($model) {
            // Hapus file fisik saat record dihapus
            foreach (['cover_image', 'og_image'] as $field) {
                $path = $model->$field;
                if ($path && Storage::disk(config('filesystems.default_upload_disk'))->exists($path)) {
                    Storage::disk(config('filesystems.default_upload_disk'))->delete($path);
                }
            }
            Cache::forget('home_activities');
            Cache::forget('activity_categories_all');
        });
    }

    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'cover_image',
        'og_image',
        'activity_date',
        'location',
        'status',
        'published_at',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ActivityCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
