<?php

namespace Spatie\DiskMonitor\Tests\Feature\Http\Controllers;

use Spatie\DiskMonitor\Models\DiskMonitorEntry;
use Spatie\DiskMonitor\Tests\TestCase;

class DiskMetricsControllerTest extends TestCase
{
    /** @test */
    public function it_can_display_the_list_of_entries()
    {
        $entry = DiskMonitorEntry::factory()->create();

        $this
            ->get('disk-monitor')
            ->assertOk()
            ->assertSee($entry->disk_name)
            ->assertSee($entry->file_count);
    }
}
