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
        Schema::create('practices', function (Blueprint $table) {
            $table->id();
            $table->tinyText('title')->nullable();
            $table->string('q_num', 50)->nullable();
            $table->time('prepare_time')->nullable();
            $table->time('test_time')->nullable();
            $table->text('paragraph')->nullable();
            $table->json('audio')->nullable();
            $table->text('image')->nullable();
            $table->boolean('is_short')->nullable();
            $table->enum('difficulty', ['Easy', 'Medium', 'Hard'])->nullable();
            $table->enum('image_type', ['Bar', 'Line', 'Pie', 'Flow', 'Table', 'Map', 'Pic', 'Comb'])->nullable();
            $table->enum('essay_type', ['Dual Q', 'Y/N', 'Open Q'])->nullable();
            $table->boolean('disable')->default(0)->nullable();
            $table->unsignedBigInteger('main_category_id')->nullable();
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->foreign('main_category_id')->references('id')->on('main_categories');
            $table->foreign('sub_category_id')->references('id')->on('sub_categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practices');
    }
};
