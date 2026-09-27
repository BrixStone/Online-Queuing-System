<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'students';

    protected $fillable = [
        'student_number',
        'first_name',
        'last_name',
        'academic_level',
        'year_level',
        'course_or_strand',
    ];

    public function transactionRequests(): HasMany
    {
        return $this->hasMany(
            TransactionRequest::class,
            'student_id'
        );
    }
}
