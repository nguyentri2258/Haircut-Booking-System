<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Availability extends Model
{
    use HasFactory;

    protected $table = 'user_availabilities';

    protected $fillable = ['user_id', 'available_date', 'time_of_day',];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}