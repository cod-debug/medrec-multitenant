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
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inpatient_id')->constrained('inpatients')->onDelete('cascade');
            $table->dateTime('admission_date');
            $table->string('referred_by')->nullable();
            $table->longText('reason_for_admission')->nullable();
            $table->string('admission_status')->nullable();
            $table->dateTime('discharge_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
