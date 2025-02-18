<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Terminal;

class CreateTerminalsCollection extends Command
{
    protected $signature = 'mongodb:create-terminals-collection';
    protected $description = 'Create terminals collection and indexes in MongoDB';

    public function handle()
    {
        Terminal::raw(function ($collection) {
            $collection->createIndex(['id' => 1], ['unique' => true]);
            $collection->createIndex(['geography_city_id' => 1]);
            $collection->createIndex(['lat' => 1, 'lon' => 1]);
            $collection->createIndex(['address_code' => 1]);
            $collection->createIndex(['city_name' => 1]);
            $collection->createIndex(['phones' => 1]);
            $collection->createIndex(['emails' => 1]);
        });

        $this->info('Terminals collection and indexes created successfully.');
    }
}
