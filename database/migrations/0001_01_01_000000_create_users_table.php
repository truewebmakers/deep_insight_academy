<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id');
            $table->text('name')->nullable();
            $table->tinyText('email')->nullable();
            $table->string('phoneno',20)->nullable();
            $table->tinyText('password')->nullable();
            $table->boolean('disable')->default(0)->comment('0:Not Disable ,1:Disable')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
