<?php

use App\Models\Terminal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class CreateTerminalsIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Artisan::call('mongodb:create-terminals-collection');
        Artisan::call('kit:sync-geography', ['--force' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Terminal::raw(function ($collection) {
            $collection->drop();
        });
    }
}
