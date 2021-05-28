<?php

use \Faker\Generator;
use Spatie\DiskMonitor\Models\DiskMonitorEntry;

/* @var Illuminate\Database\Eloquent\Factory $factory */
$factory->define(DiskMonitorEntry::class, function (Generator $faker) {
    return [
        'disk_name' => $faker->word,
        'file_count' => rand(0, 10),
        'disk_size' => rand(0, 10),
    ];
});
