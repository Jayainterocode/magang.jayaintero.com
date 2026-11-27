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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('division');
            $table->json('education_levels');
            $table->string('summary', 300);
            $table->text('description');
            $table->text('qualifications');
            $table->unsignedTinyInteger('duration_months');
            $table->unsignedSmallInteger('quota')->nullable();
            $table->date('opens_at')->nullable();
            $table->date('closes_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
