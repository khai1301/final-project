<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'title', 'subject', 'grade', 'education_level', 'skills',
        'mode', 'schedule', 'budget_min', 'budget_max', 'description',
        'status', 'location_type', 'address',
    ];

    protected $casts = [
        'skills' => 'array',
        'schedule' => 'array',
    ];
}
