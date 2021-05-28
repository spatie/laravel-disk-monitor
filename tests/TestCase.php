<?php

namespace Spatie\DiskMonitor\Tests;

use AddDiskSizeToDiskMonitorTables;
use CreateDiskMonitorTables;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\DiskMonitor\DiskMonitorServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/database/factories');

        Route::diskMonitor('disk-monitor');
    }

    protected function getPackageProviders($app)
    {
        return [
            DiskMonitorServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        include_once __DIR__.'/../database/migrations/create_disk_monitor_tables.php.stub';
        include_once __DIR__.'/../database/migrations/add_disk_size_to_disk_monitor_tables.php.stub';

        (new CreateDiskMonitorTables())->up();
        (new AddDiskSizeToDiskMonitorTables())->up();
    }
}
