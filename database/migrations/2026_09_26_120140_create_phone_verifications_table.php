<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phone_verifications', function (Blueprint $table) {
            $table->id();

            $table->string('mobile_number', 20);

            $table->string('otp_hash');

            $table->timestamp('expires_at');

            $table->unsignedInteger('attempts')->default(0);

            $table->boolean('verified')->default(false);

            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->index('mobile_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phone_verifications');
    }
};
