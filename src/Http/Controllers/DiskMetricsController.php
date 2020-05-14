<?php

namespace Spatie\DiskMonitor\Http\Controllers;

use Spatie\DiskMonitor\Models\DiskMonitorEntry;

class DiskMetricsController
{
    public function __invoke()
    {
        $entries = DiskMonitorEntry::latest()->get();

        return view('disk-monitor::entries', compact('entries'));
    }
}
