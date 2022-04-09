<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserReceptionistAntrianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_receptionist_antrian', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_receptionist_id');
            $table->uuid('antrian_id');
            $table->date('tanggal');
            $table->enum('status',['Menunggu','Memanggil','Proses','Selesai'])->default('Menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_receptionist_antrian');
    }
}
