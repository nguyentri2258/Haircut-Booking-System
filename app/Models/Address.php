<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Booking;
use App\Models\Holiday;
use App\Models\User;

class Address extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'address',
        'note'   
    ];

    public function booking()
    {
        return $this->hasMany(Booking::class);
    }

    public function holiday()
    {
        return $this->hasMany(Holiday::class);
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }
}