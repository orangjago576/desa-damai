<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes, \App\Traits\ConvertsImagesToWebp;

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->convertToWebp(['cover_image', 'og_image']);
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
