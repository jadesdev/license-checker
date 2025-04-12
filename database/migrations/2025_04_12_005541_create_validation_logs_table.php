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
        Schema::create('validation_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('access_key', 64);
            $table->string('domain');
            $table->string('url')->nullable();
            $table->string('system_fingerprint')->nullable();
            $table->string('request_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('meta')->nullable();
            $table->string('status'); // valid, invalid, expired, revoked, limit_reached, domain_mismatch
            $table->text('message')->nullable();
            $table->boolean('auto_registered')->default(false);
            $table->boolean('reset_domains')->default(false);
            $table->timestamp('registration_date')->nullable();
            $table->timestamps();

            $table->index(['access_key', 'domain', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validation_logs');
    }
};
