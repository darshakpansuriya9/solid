<?php

namespace App\Console\Commands;

use App\JobRun;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BulkXmlDownload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk_xml_download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'download bulk xml file to server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $array = [];
        for ($i = 1; $i <= 200; $i++) {
            $url = 'http://feeds.talent.com/feeds/download.php?partner=jobtimiser_bulk&country=nl&page=' . $i . '&of=200';
            $name = date('Y-m-d') . '_job_page_' . $i;
            $extensions = '.xml';
            $folder_path = '/downloadBulk/' . date('Y-m-d');

            if (!File::exists(public_path() . "/" . $folder_path)) {
                File::makeDirectory(public_path() . "/" . $folder_path, 0777, true);
            }
            $path = public_path($folder_path . '/' . $name . $extensions);
            $client = new Client();
            $resource = \GuzzleHttp\Psr7\Utils::tryFopen($path, 'w');
            $client->request('GET', $url, ['sink' => $resource]);

            $array[] = [
                'date' => date('Y-m-d'),
                'page' => $i,
                'type' => 'bulk',
                'created_at' => date('Y-m-d H:i:s')
            ];
        }
        JobRun::insert($array);
    }
}
