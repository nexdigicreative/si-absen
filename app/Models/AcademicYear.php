<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'semester',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function classes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Classes::class);
    }

    public static function active(): ?self
    {
        return static::where('is_active', true)->first();
    }

    public function getSemesterLabelAttribute(): string
    {
        return $this->semester === 1 ? 'Ganjil' : 'Genap';
    }

    public function getFullLabelAttribute(): string
    {
        return "{$this->year} Semester {$this->semester_label}";
    }
}
