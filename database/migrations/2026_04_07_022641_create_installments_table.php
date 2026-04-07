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
        Schema::create('installments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('financing_application_id');
            $table->integer('installment_number');
            $table->date('jatuh_tempo');
            $table->bigInteger('nominal_pokok');
            $table->bigInteger('nominal_bunga');
            $table->bigInteger('total_cicilan');
            $table->enum('status', ['unpaid', 'paid']);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('financing_application_id')->references('id')->on('financing_applications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
