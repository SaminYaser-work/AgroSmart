<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'date',
        'time_in',
        'time_out',
        'leave_reason'
    ];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function getHoursWorked() : float
    {
        $time_in = strtotime($this->time_in);
        $time_out = strtotime($this->time_out);
        return ($time_out - $time_in) / 3600;
    }
}
