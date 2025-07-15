<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('institute_user', function (Blueprint $table) {
            $table->foreignId('institute_id')
                ->constrained('institutes')
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('role_institute')->nullable();
            $table->timestamps();

            $table->primary(['institute_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('institute_user');
    }
};
