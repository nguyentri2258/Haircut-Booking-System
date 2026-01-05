<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Address;
use App\Models\User;
use App\Models\Service;


class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "phone",
        "email",
        "address_id",
        "user_id",
        "date",
        "notes"
    ];
    
    protected $casts = [
        'date' => 'datetime',
    ];
    

    public function address()
    {
        return $this->belongsTo(Address::class,'address_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class,'booking_service');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

}