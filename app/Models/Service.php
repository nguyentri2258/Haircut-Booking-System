<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Booking;


class Service extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'price',
        'description'    
    ];

    public function bookings()
    {
        return $this->belongsToMany(Booking::class);
    }

}