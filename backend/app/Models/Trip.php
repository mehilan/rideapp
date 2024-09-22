<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'destination' => 'array',
            'origin' => 'array',
            'driver_location' => 'array',
            'is_started' => 'boolean',
            'is_completed' => 'boolean'
        ];
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
