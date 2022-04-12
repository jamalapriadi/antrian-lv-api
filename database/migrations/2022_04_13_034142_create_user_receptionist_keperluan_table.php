<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserReceptionistKeperluanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_receptionist_keperluan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_receptionist_id');
            $table->uuid('keperluan_id');
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
        Schema::dropIfExists('user_receptionist_keperluan');
    }
}
