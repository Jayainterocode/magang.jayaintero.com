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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->foreignId('program_id')->constrained()->restrictOnDelete();
            $table->string('status')->default('submitted')->index();

            $table->string('full_name');
            $table->string('email')->index();
            $table->string('phone', 30);
            $table->date('birth_date');
            $table->string('gender');
            $table->string('address', 500);

            $table->string('education_level');
            $table->string('institution');
            $table->string('major');
            $table->string('semester', 30);
            $table->string('student_number', 50);
            $table->string('supervisor_name')->nullable();
            $table->string('supervisor_phone', 30)->nullable();

            $table->date('start_date');
            $table->date('end_date');
            $table->text('motivation');

            $table->timestamp('consent_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
