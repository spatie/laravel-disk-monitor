<?php

namespace Spatie\DiskMonitor\Tests\Feature\Http\Controllers;

use Spatie\DiskMonitor\Tests\TestCase;

class DiskMetricsControllerTest extends TestCase
{
    /** @test */
    public function it_can_display_the_list_of_entries()
    {
        $this
            ->get('disk-monitor')
            ->assertOk();
    }
}
