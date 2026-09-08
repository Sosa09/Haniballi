<?php

namespace App\Models;

use Database\Factories\NutritionPlanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NutritionPlan extends Model
{
    /** @use HasFactory<NutritionPlanFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'patient_id',
        'user_id',
        'title',
        'description',
        'daily_meals',
        'daily_calories',
        'protein_grams',
        'carbs_grams',
        'fat_grams',
        'start_date',
        'end_date',
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
            'daily_meals' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
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
