<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Antrian extends Model
{
    use HasFactory;

    protected $table="antrians";

    public function keperluan(){
        return $this->belongsTo(Keperluan::class,'keperluan_id');
    }
}
