<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 60);
            $table->string('last_name', 60);
            $table->string('email')->unique();
            $table->string('city', 60);
            $table->string('profile', 30);
            $table->string('comment', 280)->nullable();
            $table->boolean('show_name')->default(true);
            $table->boolean('accepts_updates')->default(false);
            $table->string('ip_hash', 64)->nullable();
            $table->timestamp('verified_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signatures');
    }
};
