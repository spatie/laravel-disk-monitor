<?php

namespace Spatie\DiskMonitor\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Spatie\DiskMonitor\Models\DiskMonitorEntry;

class RecordDiskMetricsCommand extends Command
{
    public $signature = 'disk-monitor:record-metrics';

    public $description = 'Record the metrics of a disk';

    public function handle()
    {
        collect(config('disk-monitor.disk_names'))
            ->each(fn (string $diskName) => $this->recordMetrics($diskName));

        $this->comment('All done!');
    }

    protected function recordMetrics(string $diskName): void
    {
        $this->info("Recording metrics for disk `{$diskName}`...");

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk($diskName);

        $fileCount = count($disk->allFiles());
        $diskSize = $this->getFolderSize($disk->path(''));

        DiskMonitorEntry::create([
            'disk_name' => $diskName,
            'file_count' => $fileCount,
            'disk_size' => $diskSize,
        ]);
    }

    protected function getFolderSize(string $path): int
    {
        $size = 0;

        foreach (glob(rtrim($path, '/') . '/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->getFolderSize($each);
        }

        return $size;
    }
}
