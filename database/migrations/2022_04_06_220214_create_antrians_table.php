<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAntriansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('antrians', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type',[1,2])->default(2); //1 adalah prioritas, 2 adalah umum
            $table->date('tanggal')->nullable();
            $table->string('no_antrian')->nullable();
            $table->uuid('keperluan_id')->nullable();
            $table->enum('is_open',['N','Y'])->default('N');
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
        Schema::dropIfExists('antrians');
    }
}
