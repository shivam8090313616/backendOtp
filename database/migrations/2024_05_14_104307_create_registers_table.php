<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistersTable extends Migration
{
    public function up()
    {
        Schema::create('registers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('f_name');
            $table->string('l_name');
            $table->string('mobile');
            $table->string('messenger');
            $table->string('password');
            $table->string('confirmpassword');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('registers');
    }
}

