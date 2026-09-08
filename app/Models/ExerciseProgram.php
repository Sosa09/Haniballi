<?php

namespace App\Models;

use Database\Factories\ExerciseProgramFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseProgram extends Model
{
    /** @use HasFactory<ExerciseProgramFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'patient_id',
        'user_id',
        'title',
        'description',
        'category',
        'difficulty',
        'duration_minutes',
        'exercises',
        'sessions_per_week',
        'video_url',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'exercises' => 'array',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
