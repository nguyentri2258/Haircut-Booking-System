<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Address;

class Holiday extends Model
{
    protected $fillable = ['date', 'note', 'address_id'];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}