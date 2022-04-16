<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceptionistAudio extends Model
{
    use HasFactory;

    protected $table="receptionist_audio";

    public function receptionist(){
        return $this->belongsTo(Receptionist::class,'receptionist_id');
    }
}
