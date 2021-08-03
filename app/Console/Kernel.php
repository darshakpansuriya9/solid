<?php

namespace App\Console;

use App\Console\Commands\BulkXmlDownload;
use App\Console\Commands\BulkXmlUpload;
use App\Console\Commands\SingleXmlDownload;
use App\Console\Commands\SingleXmlUpload;
use App\Console\Commands\XmlExport;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        BulkXmlUpload::class,
        BulkXmlDownload::class,
        SingleXmlDownload::class,
        XmlExport::class,
        SingleXmlUpload::class,
    ];

    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('bulk_xml_upload')
//            ->everyMinute();
//        $schedule->command('bulk_xml_download')
//            ->dailyAt('22:00');
        $schedule->command('xml_export')
            ->dailyAt('22:00');
//        $schedule->command('single_xml_download')
//            ->dailyAt('22:00');
//        $schedule->command('single_xml_upload')
//            ->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
