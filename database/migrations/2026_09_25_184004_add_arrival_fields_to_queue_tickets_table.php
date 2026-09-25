<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queue_tickets', function (Blueprint $table) {

            /*
             * When the ticket becomes "serving".
             * This starts the 3-minute timer.
             */
            $table->timestamp('serving_started_at')
                ->nullable()
                ->after('status');


            /*
             * When the student presses "I'm Here".
             */
            $table->timestamp('arrived_at')
                ->nullable()
                ->after('serving_started_at');

        });
    }

    public function down(): void
    {
        Schema::table('queue_tickets', function (Blueprint $table) {

            $table->dropColumn([
                'serving_started_at',
                'arrived_at',
            ]);

        });
    }
};
