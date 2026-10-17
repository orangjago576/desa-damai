<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use InvalidArgumentException;

class PointOfInterest extends Model
{
    use HasFactory, SoftDeletes, \App\Traits\ConvertsImagesToWebp;

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->convertToWebp(['photo_path']);
        });
    }

    protected $table = 'points_of_interest';

    protected $fillable = [
        'name',
        'category',
        'address',
        'hamlet',
        'latitude',
        'longitude',
        'description',
        'photo_path',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function setLatitudeAttribute($value): void
    {
        $floatVal = (float) $value;
        if ($floatVal < -90.0 || $floatVal > 90.0) {
            throw new InvalidArgumentException('Latitude harus bernilai antara -90 dan 90.');
        }

        $this->attributes['latitude'] = $value;
    }

    public function setLongitudeAttribute($value): void
    {
        $floatVal = (float) $value;
        if ($floatVal < -180.0 || $floatVal > 180.0) {
            throw new InvalidArgumentException('Longitude harus bernilai antara -180 dan 180.');
        }

        $this->attributes['longitude'] = $value;
    }
}
