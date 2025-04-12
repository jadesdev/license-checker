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
        Schema::create('access_keys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('key')->unique();
            $table->string('owner_name')->nullable();
            $table->string('owner_email')->nullable();
            $table->json('allowed_domains')->nullable(); // JSON array of allowed domains
            $table->integer('max_domains')->default(1);
            $table->string('tier')->default('standard'); // standard, premium, enterprise
            $table->json('features')->nullable(); // JSON array of enabled features
            $table->json('metadata')->nullable(); // JSON array of additional metadata
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->boolean('revoked')->default(false);
            $table->text('revocation_reason')->nullable();
            $table->boolean('allow_auto_registration')->default(true);
            $table->boolean('allow_localhost')->default(true);
            $table->integer('grace_period_hours')->default(72);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_keys');
    }
};
