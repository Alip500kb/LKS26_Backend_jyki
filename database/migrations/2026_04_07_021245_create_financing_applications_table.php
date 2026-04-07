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
        Schema::create('financing_applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('business_verification_id');
            $table->bigInteger('jumlah_pembiayaan');
            $table->integer('tenor_bulan');
            $table->text('tujuan_pembiayaan');
            $table->integer('skor_kelayakan')->nullable();
            $table->bigInteger('rekomendasi_limit')->nullable();
            $table->text('catatan_analisis')->nullable();
            $table->enum('status', ['under_review', 'submitted', 'recommended', 'rejected_by_analyst', 'rejected_by_manager', 'approved']);
            $table->timestamp('submitted_at');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('business_verification_id')->references('id')->on('business_verifications');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financing_applications');
    }
};
