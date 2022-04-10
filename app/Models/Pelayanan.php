<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pelayanan extends Model
{
    use HasFactory;

    protected $table="pelayanans";

    public function antrian(){
        return $this->belongsTo(Antrian::class,'antrian_id');
    }
}
