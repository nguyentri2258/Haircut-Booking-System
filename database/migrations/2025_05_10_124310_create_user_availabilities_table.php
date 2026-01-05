<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('available_date');
            $table->string('time_of_day');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_availabilities');
    }
};
