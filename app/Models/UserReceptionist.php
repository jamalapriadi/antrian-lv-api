<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserReceptionist extends Model
{
    use HasFactory;

    protected $table="user_receptionist";

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function receptionis(){
        return $this->belongsTo(Receptionist::class,'receptionist_id');
    }

    public function keperluan(){
        return $this->hasMany(UserReceptionistKeperluan::class,'user_receptionist_id');
    }
}
