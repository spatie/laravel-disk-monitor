# Monitor metrics of Laravel disks

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-disk-monitor.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-disk-monitor)
![Test Statusd](https://github.com/spatie/laravel-disk-monitor/workflows/run-tests/badge.svg)
![Code Style Status](https://github.com/spatie/laravel-disk-monitor/workflows/Check%20&%20fix%20styling/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-disk-monitor.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-disk-monitor)

laravel-disk-monitor can monitor the usage of the filesystems configured in Laravel. Currently only the amount of files a disk contains is monitored.

This package was initially build in the "Let's build a package together" video of the [Laravel Package Training](https://laravelpackage.training) video course.

If you've seen the entire video course, and want to practice creating a PR on this repo, please do so!

## Support us

[![Image](https://github-ads.s3.eu-central-1.amazonaws.com/laravel-disk-monitor.jpg)](https://spatie.be/github-ad-click/laravel-disk-monitor)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/laravel-disk-monitor
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Spatie\DiskMonitor\DiskMonitorServiceProvider" --tag="migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Spatie\DiskMonitor\DiskMonitorServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [
    /*
     * The names of the disk you want to monitor.
     */
    'disk_names' => [
        'local',
    ],
];
```

Finally, you should schedule the `Spatie\DiskMonitor\Commands\RecordsDiskMetricsCommand` to run daily.

```php
// in app/Console/Kernel.php

use \Spatie\DiskMonitor\Commands\RecordsDiskMetricsCommand;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
       // ...
        $schedule->command(RecordsDiskMetricsCommand::class)->daily();
    }
}

```

## Usage

You can view the amount of files each monitored disk has, in the `disk_monitor_entries` table.

If you want to view the statistics in the browser add this macro to your routes file.

```php
// in a routes files

Route::diskMonitor('my-disk-monitor-url');
```

Now, you can see all statics when browsing `/my-disk-monitor-url`. Of course, you can use any url you want when using the `diskMonitor` route macro. We highly recommand using the `auth` middleware for this route, so guests can't see any data regarding your disks.

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
