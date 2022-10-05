<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use laranex\LaravelMyanmarNRC\Models\State;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nrc_townships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nrc_state_id');
            $table->string('code');
            $table->string('code_mm');
            $table->string('name');
            $table->string('name_mm');
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
        Schema::dropIfExists('nrc_townships');
    }
};
