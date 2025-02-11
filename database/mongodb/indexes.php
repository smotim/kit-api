<?php

return [
    \App\Models\City::class => [
        ['key' => ['name' => 'text', 'region' => 'text']],
        ['key' => ['city_id' => 1], 'unique' => true],
    ],
    \App\Models\Terminal::class => [
        ['key' => ['name' => 'text', 'address' => 'text']],
        ['key' => ['terminal_id' => 1], 'unique' => true],
        ['key' => ['city_id' => 1]],
    ],
];