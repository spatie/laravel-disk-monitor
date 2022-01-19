<?php

namespace Spatie\DiskMonitor;

use Illuminate\Support\Facades\Route;
use Spatie\DiskMonitor\Commands\RecordDiskMetricsCommand;
use Spatie\DiskMonitor\Http\Controllers\DiskMetricsController;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DiskMonitorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-disk-monitor')
            ->hasViews()
            ->hasConfigFile()
            ->hasMigration('create_disk_monitor_tables')
            ->hasCommands(RecordDiskMetricsCommand::class);
    }

    public function packageRegistered()
    {
        Route::macro('diskMonitor', function (string $prefix) {
            Route::prefix($prefix)->group(function () {
                Route::get('/', '\\' . DiskMetricsController::class);
            });
        });
    }
}
