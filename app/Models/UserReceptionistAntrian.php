<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReceptionistAntrian extends Model
{
    use HasFactory;

    protected $table="user_receptionist_antrian";

    public function antrian(){
        return $this->belongsTo(Antrian::class,'antrian_id');
    }
}
