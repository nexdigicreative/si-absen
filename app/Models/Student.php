<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nis',
        'nisn',
        'name',
        'gender',
        'dob',
        'pob',
        'address',
        'class_id',
        'parent_name',
        'parent_phone',
        'parent_email',
        'photo',
        'user_id',
        'status',
    ];

    protected $casts = [
        'dob' => 'date',
        'status' => 'boolean',
    ];

    // ── Relations ──────────────────────────────────────────
    public function class(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AttendanceDetail::class);
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    // ── Helpers ─────────────────────────────────────────────
    public function getGenderLabelAttribute(): string
    {
        return $this->gender === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    public function getAgeAttribute(): int
    {
        return $this->dob ? $this->dob->age : 0;
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=8b5cf6&color=fff';
    }

    public function getAttendancePercentageAttribute(): float
    {
        $total = $this->attendanceDetails()->count();
        $present = $this->attendanceDetails()
            ->whereIn('status', ['hadir', 'terlambat'])->count();

        return $total > 0 ? round($present / $total * 100, 1) : 0;
    }
}