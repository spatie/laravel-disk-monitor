<?php

namespace Spatie\DiskMonitor;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Spatie\DiskMonitor\Commands\RecordDiskMetricsCommand;
use Spatie\DiskMonitor\Http\Controllers\DiskMetricsController;

class DiskMonitorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this
            ->registerPublishables()
            ->registerCommands()
            ->registerRoutes()
            ->registerViews();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/disk-monitor.php', 'disk-monitor');
    }

    protected function registerPublishables(): self
    {
        if (! $this->app->runningInConsole()) {
            return $this;
        }
        $this->publishes([
            __DIR__ . '/../config/disk-monitor.php' => config_path('disk-monitor.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../resources/views' => base_path('resources/views/vendor/disk-monitor'),
        ], 'views');

        $this->registerMigrations();

        return $this;
    }

    protected function registerCommands(): self
    {
        if (! $this->app->runningInConsole()) {
            return $this;
        }

        $this->commands([
            RecordDiskMetricsCommand::class,
        ]);

        return $this;
    }

    protected function registerRoutes(): self
    {
        Route::macro('diskMonitor', function (string $prefix) {
            Route::prefix($prefix)->group(function () {
                Route::get('/', '\\' . DiskMetricsController::class);
            });
        });

        return $this;
    }

    protected function registerViews(): self
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'disk-monitor');

        return $this;
    }

    protected function registerMigrations(): void
    {
        $migrationFileNames = [
            'create_disk_monitor_tables',
            'add_disk_size_to_disk_monitor_tables',
        ];

        $now = Carbon::now();
        foreach ($migrationFileNames as $migrationFileName) {
            if (! $this->migrationFileExists($migrationFileName)) {
                $this->publishes([
                    __DIR__ . "/../database/migrations/{$migrationFileName}.php.stub" => with($migrationFileName, function ($migrationFileName) use ($now) {
                        $migrationPath = 'migrations/';

                        if (Str::contains($migrationFileName, '/')) {
                            $migrationPath = Str::finish(Str::beforeLast($migrationFileName, '/'), '/');
                            $migrationFileName = Str::afterLast($migrationFileName, '/');
                        }

                        return database_path($migrationPath . $now->addSecond()->format('Y_m_d_His') . '_' . Str::finish(Str::snake($migrationFileName), '.php'));
                    }),
                ], 'migrations');
            }
        }
    }

    protected function migrationFileExists(string $migrationFileName): bool
    {
        $migrationsPath = 'migrations/';

        $len = strlen($migrationFileName) + 4;

        if (Str::contains($migrationFileName, '/')) {
            $migrationsPath = Str::finish(Str::beforeLast($migrationFileName, '/'), '/');
            $migrationFileName = Str::afterLast($migrationFileName, '/');
        }

        foreach (glob(database_path("${migrationsPath}*.php")) as $filename) {
            if ((substr($filename, -$len) === $migrationFileName . '.php')) {
                return true;
            }
        }

        return false;
    }
}
