<?php

use Spatie\DiskMonitor\Models\DiskMonitorEntry;

it('can display the list of entries', function () {
    $entry = DiskMonitorEntry::factory()->create();

    $this
        ->get('disk-monitor')
        ->assertOk()
        ->assertSee($entry->disk_name)
        ->assertSee($entry->file_count);
});
