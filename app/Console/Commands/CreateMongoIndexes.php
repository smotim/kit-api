<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MongoDB\Laravel\Collection;

class CreateMongoIndexes extends Command
{
    protected $signature = 'mongodb:create-indexes';
    protected $description = 'Create MongoDB indexes';

    public function handle()
    {
        $indexes = require database_path('mongodb/indexes.php');

        foreach ($indexes as $model => $modelIndexes) {
            $collection = app($model)->getCollection();

            foreach ($modelIndexes as $index) {
                $collection->createIndex($index['key'], array_diff_key($index, ['key' => 1]));
            }

            $this->info("Created indexes for {$model}");
        }

        return 0;
    }
}