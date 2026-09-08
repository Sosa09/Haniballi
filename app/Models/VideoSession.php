<?php

namespace App\Models;

use Database\Factories\VideoSessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoSession extends Model
{
    /** @use HasFactory<VideoSessionFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'appointment_id',
        'room_name',
        'room_url',
        'doctor_token',
        'patient_token',
        'provider',
        'status',
        'started_at',
        'ended_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
