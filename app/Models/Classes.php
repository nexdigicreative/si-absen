<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'grade',
        'major',
        'homeroom_teacher_id',
        'academic_year_id',
        'room',
        'max_students',
    ];

    // ── Relations ──────────────────────────────────────────
    public function homeroomTeacher(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id');
    }

    public function academicYear(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function students(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function schedules(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }

    public function attendances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }

    // ── Helpers ─────────────────────────────────────────────
    public function getStudentCountAttribute(): int
    {
        return $this->students()->where('status', true)->count();
    }

    public function getGradeLabelAttribute(): string
    {
        return match ($this->grade) {
            10 => 'X', 11 => 'XI', 12 => 'XII', default => (string) $this->grade,
        };
    }
}
