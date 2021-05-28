<?php

namespace Spatie\DiskMonitor\Tests\Feature\Commands;

use Illuminate\Support\Facades\Storage;
use Spatie\DiskMonitor\Commands\RecordDiskMetricsCommand;
use Spatie\DiskMonitor\Models\DiskMonitorEntry;
use Spatie\DiskMonitor\Tests\TestCase;

class RecordDiskMetricsCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('anotherDisk');
    }

    /** @test */
    public function it_will_record_the_file_count_for_a_single_disk()
    {
        $this->artisan(RecordDiskMetricsCommand::class)->assertExitCode(0);
        $entry = DiskMonitorEntry::last();
        $this->assertEquals(0, $entry->file_count);

        Storage::disk($entry->disk_name)->put('test.txt', 'text');
        $this->artisan(RecordDiskMetricsCommand::class)->assertExitCode(0);
        $entry = DiskMonitorEntry::last();
        $this->assertEquals(1, $entry->file_count);
    }

    /** @test */
    public function it_will_record_the_file_count_for_multiple_disks()
    {
        config()->set('disk-monitor.disk_names', ['local', 'anotherDisk']);
        Storage::disk('anotherDisk')->put('test.txt', 'text');

        $this->artisan(RecordDiskMetricsCommand::class)->assertExitCode(0);

        $entries = DiskMonitorEntry::get();
        $this->assertCount(2, $entries);

        $this->assertEquals('local', $entries[0]->disk_name);
        $this->assertEquals(0, $entries[0]->file_count);

        $this->assertEquals('anotherDisk', $entries[1]->disk_name);
        $this->assertEquals(1, $entries[1]->file_count);
    }

    /** @test */
    public function it_will_record_the_disk_size_for_a_single_disk()
    {
        $localDisk = Storage::disk('local');
        $localDisk->put('test.txt', '12345');
        $localDisk->makeDirectory('sub-directory');
        $localDisk->put('sub-directory/test-sub.txt', '1234567890');

        $this->artisan(RecordDiskMetricsCommand::class)->assertExitCode(0);

        $entry = DiskMonitorEntry::first();
        $this->assertEquals(15, $entry->disk_size);
    }

    /** @test */
    public function it_will_record_the_disk_size_for_multiple_disks()
    {
        config()->set('disk-monitor.disk_names', ['local', 'anotherDisk']);

        $localDisk = Storage::disk('local');
        $localDisk->put('local.txt', '1234567');
        $localDisk->makeDirectory('sub-directory');
        $localDisk->put('sub-directory/test-sub.txt', '1234567890');

        $anotherDisk = Storage::disk('anotherDisk');
        $anotherDisk->put('anotherDisk.txt', '123456789');
        $anotherDisk->makeDirectory('sub-directory');
        $anotherDisk->put('sub-directory/test-sub.txt', '12345');

        $this->artisan(RecordDiskMetricsCommand::class)->assertExitCode(0);

        $entries = DiskMonitorEntry::get();

        $this->assertEquals(17, $entries[0]->disk_size);
        $this->assertEquals(14, $entries[1]->disk_size);
    }
}
