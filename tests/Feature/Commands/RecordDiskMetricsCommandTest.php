<?php

use Illuminate\Support\Facades\Storage;
use Spatie\DiskMonitor\Commands\RecordDiskMetricsCommand;
use Spatie\DiskMonitor\Models\DiskMonitorEntry;

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('anotherDisk');
});

it('will record the file count for a single disk', function () {
    $this->artisan(RecordDiskMetricsCommand::class)->assertExitCode(0);
    $entry = DiskMonitorEntry::last();
    expect($entry->file_count)->toEqual(0);

    Storage::disk($entry->disk_name)->put('test.txt', 'text');
    $this->artisan(RecordDiskMetricsCommand::class)->assertExitCode(0);
    $entry = DiskMonitorEntry::last();
    expect($entry->file_count)->toEqual(1);
});

it('will record the file count for multiple disks', function () {
    config()->set('disk-monitor.disk_names', ['local', 'anotherDisk']);
    Storage::disk('anotherDisk')->put('test.txt', 'text');

    $this->artisan(RecordDiskMetricsCommand::class)->assertExitCode(0);

    $entries = DiskMonitorEntry::get();
    expect($entries)->toHaveCount(2);

    expect($entries[0])
        ->disk_name->toEqual('local')
        ->file_count->toEqual(0);

    expect($entries[1])
        ->disk_name->toEqual('anotherDisk')
        ->file_count->toEqual(1);
});
