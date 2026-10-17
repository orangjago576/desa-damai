<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
