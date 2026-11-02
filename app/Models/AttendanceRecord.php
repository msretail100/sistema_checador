<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    public const TYPE_ENTRY = 'entrada';

    public const TYPE_EXIT = 'salida';

    protected $fillable = [
        'user_id',
        'tipo',
        'hora_servidor',
        'ip_address',
        'latitud',
        'longitud',
    ];

    protected function casts(): array
    {
        return [
            'hora_servidor' => 'datetime',
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
