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
        Schema::create('class_payments', function (Blueprint $table) {
            $table->id();
            $table->string('student_id');
            $table->string('payment_id')->nullable();
            $table->string('dateTime');
            $table->string('subject_id');
            $table->string('grade_id');
            $table->string('teacher_id');
            $table->string('bank');
            $table->string('transferSlip');
            $table->date('pay_month');
            $table->string('payment_type');
            $table->float('fee', 8, 2);
            $table->string('class_type');
            $table->string('status')->default('Temporarily');
            $table->string('approved_by')->nullable();
            $table->string('approved_at')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_payments');
    }
};
