<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receptionist extends Model
{
    use HasFactory;

    protected $table="receptionists";

    public function audio(){
        return $this->hasMany(ReceptionistAudio::class,'receptionist_id');
    }
}
