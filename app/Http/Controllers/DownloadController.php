<?php

namespace App\Http\Controllers;


use App\Job;
use App\JobRun;
use App\Setting;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Batch;

class DownloadController extends Controller
{

    public function xml_export()
    {
        $array = [];
        $i = 1;
        $single_xml_page = Setting::where('meta_key', 'single_xml_page')->first()->meta_value;
        while ($i <= $single_xml_page) {
            $url = 'http://feeds.talent.com/feeds/download.php?partner=jobtimiser&country=nl&page=' . $i . '&of=' . $single_xml_page;
            $name = date('Y-m-d') . '_job_page_' . $i;
            $extensions = '.xml';
            $folder_path = '/download/' . date('Y-m-d');

            if (!File::exists(public_path() . "/" . $folder_path)) {
                File::makeDirectory(public_path() . "/" . $folder_path, 0777, true);
            }
            $path = public_path($folder_path . '/' . $name . $extensions);
            $client = new Client();
            $resource = \GuzzleHttp\Psr7\Utils::tryFopen($path, 'w');
            $client->request('GET', $url, ['sink' => $resource]);

            $array = [
                'date' => date('Y-m-d'),
                'page' => $i,
                'type' => 'single',
                'created_at' => date('Y-m-d H:i:s')
            ];
            JobRun::insert($array);
            $this->save($i);
            $i++;
        }
        $this->downloadBulk();
    }

    public function downloadBulk()
    {
        $array = [];
        $i = 1;
        $bulk_xml_page = Setting::where('meta_key', 'bulk_xml_page')->first()->meta_value;
        while ($i <= $bulk_xml_page) {
            $url = 'http://feeds.talent.com/feeds/download.php?partner=jobtimiser_bulk&country=nl&page=' . $i . '&of=' . $bulk_xml_page;
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
            $array = [
                'date' => date('Y-m-d'),
                'page' => $i,
                'type' => 'bulk',
                'created_at' => date('Y-m-d H:i:s')
            ];
            JobRun::insert($array);
            $response = $this->saveBulk($i);
            if ($response['success'] == false) {
                echo "something went wrong with ." . $response['message'];
                break;
            }
        }
    }

    public function save_old()
    {
        ini_set('max_execution_time', 12010);
        $date = date('Y-m-d');
        $pendingJobs = JobRun::where('date', $date)
            ->where('type', 'single')
            ->where('is_added', 0)
            ->get()
            ->take(5);
        if (count($pendingJobs) > 0) {
            DB::beginTransaction();
            try {
                foreach ($pendingJobs as $pendingJob) {
                    $page = $pendingJob->page;
                    $path = public_path('download/' . $date . '/' . $date . '_job_page_' . $page . '.xml');
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
                }
                $response['message'] = 'The job is created.';
            } catch (\Exception $exception) {
                DB::rollback();
                $response['message'] = $exception->getMessage();
            }
            Log::info($response);
            dd($response);
        }

    }

    public function save($page_no)
    {
        ini_set('max_execution_time', 12010);
        $date = date('Y-m-d');
        DB::beginTransaction();
        try {
            $path = public_path('download/' . $date . '/' . $date . '_job_page_' . $page_no . '.xml');
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
            $pendingJob = JobRun::where('type', 'single')
                ->where('date', date('Y-m-d'))
                ->where('page', $page_no)
                ->first();

            $pendingJob->is_added = 1;
            $pendingJob->save();
            DB::commit();

            $response['message'] = 'The job is created.';
            $response['success'] = true;
        } catch (\Exception $exception) {
            DB::rollback();
            $response['message'] = $exception->getMessage();
            $response['success'] = false;
        }
        Log::info($response);
        return $response;
    }

    public function saveBulk($page_no)
    {
        DB::beginTransaction();
        try {
            ini_set('max_execution_time', 1200);
            $date = date('Y-m-d');
            $path = public_path('downloadBulk/' . $date . '/' . $date . '_job_page_' . $page_no . '.xml');
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
            $pendingJob = JobRun::where('type', 'bulk')
                ->where('date', date('Y-m-d'))
                ->where('page', $page_no)
                ->first();
            $pendingJob->is_added = 1;
            $pendingJob->save();
            DB::commit();
            $response['message'] = 'The job is created.';
            $response['success'] = true;
        } catch (\Exception $exception) {
            DB::rollback();
            $response['message'] = $exception->getMessage();
            $response['success'] = false;
        }
        Log::info($response);
        return $response;
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
