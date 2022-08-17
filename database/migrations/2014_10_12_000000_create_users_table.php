<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('age')->nullable();;
            $table->boolean('gender');
            $table->boolean('userType');
            $table->text('description');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('pictures', function (Blueprint $table) {
            $table->id();
            $table->string('picURL');
            $table->string('user_id');
            $table->boolean('pending');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('from');
            $table->string('to');
            $table->string('body');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->string('from');
            $table->string('to');
            $table->string('body');
            $table->boolean('pending');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('favorite_requests', function (Blueprint $table) {
            $table->id();
            $table->string('from');
            $table->string('to');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('user_1');
            $table->string('user_2');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
