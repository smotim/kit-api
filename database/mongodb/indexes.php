<?php

return [
    \App\Models\Terminal::class => [
        ['key' => ['name' => 'text', 'address' => 'text']],
        ['key' => ['terminal_id' => 1], 'unique' => true],
        ['key' => ['city_id' => 1]],
    ],
];