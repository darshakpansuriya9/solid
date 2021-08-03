<?php

namespace App\Console\Commands;

use App\Job;
use App\JobRun;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Batch;

class BulkXmlUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk_xml_upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'insert data from xml file to database';

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
        ini_set('max_execution_time', 1200);
        $date = date('Y-m-d');
        try {
            $pendingJobs = JobRun::where('date', $date)
                ->where('type', 'bulk')
                ->where('is_added', 0)
                ->get()
                ->take(5);
            if (count($pendingJobs) > 0) {
                foreach ($pendingJobs as $pendingJob) {
                    $page = $pendingJob->page;
                    $path = public_path('downloadBulk/' . $date . '/' . $date . '_job_page_' . $page . '.xml');
                    $xmlObject = simplexml_load_file($path, 'SimpleXMLElement', LIBXML_NOCDATA);
                    $jsonData = json_encode($xmlObject);
                    $jsonData = json_decode($jsonData);
                    $insertArray = $updateArray = $ids = [];
                    foreach ($jsonData->job as $value) {
                        if (!empty($value->city)) {
                            $result = $this->getLatLong($value->city);
                            preg_match('#\((.*?)\)#', $result, $match);
                            $latitude = '';
                            $longitude = '';
                            if (!empty($match)) {
                                $latitudeLogitudeData = explode(' ', $match[1]);
                                $latitude = isset($latitudeLogitudeData) ? number_format($latitudeLogitudeData[0], 8) : null;
                                $longitude = isset($latitudeLogitudeData) ? number_format($latitudeLogitudeData[1], 8) : null;
                            }
                            if (!empty($latitude) && !empty($longitude)) {
                                if (Job::where('job_id', $value->jobid)->count() == 0) {
                                    $insertArray[] = [
                                        'job_id' => $value->jobid,
                                        'jobURL' => $value->url ? ltrim($value->url) : null,
                                        'jobBoard' => 1,
                                        'customer_id' => 2,
                                        'functionExternal' => 1,
                                        'jobLocationCity' => $value->city,
                                        'jobLocationLat' => $latitude,
                                        'jobLocationLong' => $longitude,
                                        'functionTitle' => $value->title,
                                        'functionDescription' => !empty(ltrim($value->description)) ? ltrim($value->description) : null,
                                        'organisationLogo' => !empty(ltrim($value->logo)) ? ltrim($value->logo) : null,
                                        'organisationName' => !empty($value->company) ? $value->company : null,
                                    ];
                                } else {
                                    $job = Job::where('job_id', $value->jobid)->first();
                                    $ids[] = $value->jobid;
                                    $updateArray[] = [
                                        'id' => $job->jd,
                                        'job_id' => $value->jobid,
                                        'jobURL' => $value->url ? ltrim($value->url) : null,
                                        'jobBoard' => 1,
                                        'customer_id' => 2,
                                        'functionExternal' => 1,
                                        'jobLocationCity' => $value->city,
                                        'jobLocationLat' => $latitude,
                                        'jobLocationLong' => $longitude,
                                        'functionTitle' => $value->title,
                                        'functionDescription' => !empty(ltrim($value->description)) ? ltrim($value->description) : null,
                                        'organisationLogo' => !empty(ltrim($value->logo)) ? ltrim($value->logo) : null,
                                        'organisationName' => !empty($value->company) ? $value->company : null,
                                    ];
                                }

                            }
                        }
                    }
                    Job::insert($insertArray);
                    if (count($ids) > 0) {
                        Batch::update(new Job, $updateArray, 'id');
                    }
                    $pendingJob->is_added = 1;
                    $pendingJob->save();
                    DB::commit();
                    Log::info('job inserted.');
                }
            }
        } catch (\Exception $exception) {
            Log::info($exception);
        }

    }

    public function getLatLong($pincode = null, $city = null)
    {

        # code...
        $q = "";
        $t = "";
        if (isset($pincode) && $pincode != '-') {
            $q = $pincode;
            $t = 'postcode'; // postalcode
        } else {
            $q = $city;
            $t = 'woonplaats';
        }

        $client = new Client();
        $response = $client->request('GET', "https://geodata.nationaalgeoregister.nl/locatieserver/v3/free?q='+$q+'&type:'+$t+'");
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $body = json_decode($body, true);

        if ($body['response']['numFound'] > 0 && $statusCode == '200') {
            $result = $body['response']['docs'][0]['centroide_ll'];
            return ($result);
        } else {
            return 'Not Valid.';
        }
    }
}
