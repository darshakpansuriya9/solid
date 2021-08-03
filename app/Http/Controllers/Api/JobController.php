<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Customer;
use DB;
use App\Job;
use Validator;
use Symfony\Component\Console\Input\Input;
use GuzzleHttp\client;
// use Input;
class JobController extends Controller
{

    /**
     * @OA\POST(
     *      path="/api/jobs/create",
     *      tags={"Jobs"},
     *      operationId="job.create",
     *
     *      summary="Create a new job.",
     *      @OA\Parameter(
     *          name="api-key",
     *          in="header",
     *          description="bearer auth",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="jobId",
     *          in="query",
     *          description="the id of the job.",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="jobURL",
     *          in="query",
     *          description="the URL to the job.",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="applicationURL",
     *          in="query",
     *          description="the URL to the application form.",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="functionTitle",
     *          in="query",
     *          description="the title of the job",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="functionOneliner",
     *          in="query",
     *          description="the oneliner of the job",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="functionDescription",
     *          in="query",
     *          description="the description of the job.",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="educationalDirection",
     *          in="query",
     *          description="the desired education.",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="educationalLevel",
     *          in="query",
     *          description="the desired level of education.",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="experienceLevel",
     *          in="query",
     *          description="the desired level of experience.",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="contractType",
     *          in="query",
     *          description="the type of contract.",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="workingHours",
     *          in="query",
     *          description="the amount of working hours per week.",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="functionGroup",
     *          in="query",
     *          description="the functiongroup of the job.",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="branches",
     *          in="query",
     *          description="the branche the company is active.",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="salaryMin",
     *          in="query",
     *          description="the minimum salary.",
     *          required=false,
     *          @OA\Schema(
     *              type="number"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="salaryMax",
     *          in="query",
     *          description="the maximum salary.",
     *          required=false,
     *          @OA\Schema(
     *              type="number"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="salaryPeriod",
     *          in="query",
     *          description="the payment period of the salary.",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="salaryCurrency",
     *          in="query",
     *          description="the currency denomonation of the salary.",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="jobLocationCountry",
     *          in="query",
     *          description="the location of the job (country).",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="jobLocationPostalcode",
     *          in="query",
     *          description="the location of the job (postalcode).",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="jobLocationCity",
     *          in="query",
     *          description="the location of the job (city).",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="organisationName",
     *          in="query",
     *          description="the name of the organisation.",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="organisationWebsite",
     *          in="query",
     *          description="the website of the organisation.",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="organisationLogo",
     *          in="query",
     *          description="the logo of the organisation.",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="recruiterName",
     *          in="query",
     *          description="the name of the recruiter.",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="recruiterEmail",
     *          in="query",
     *          description="the emailaddress of the recruiter.",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="recruiterPhonenumber",
     *          in="query",
     *          description="the phonenumber of the recruiter.",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="jobBoard",
     *          in="query",
     *          description="the jobboard to post the job on.",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="topJob",
     *          in="query",
     *          required=false,
     *          @OA\Schema(
     *              type="boolean"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="The job is created.",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Access token is missing or invalid",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Job already exists."
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Job contains validation errors."
     *      ),
     *  )
     */
    public function createJob(Request $request){

        $input = $request->all();
        $apiKey = $request->header('api-key');

        // validation
        $validator = Validator::make($request->all(), [
            'jobId' => 'required|string|unique:jobs,job_id',
            'jobURL' => 'required|string',
            'applicationURL' => 'string',
            'functionTitle' => 'required|string',
            'functionOneliner' => 'string',
            'functionDescription' => 'required|string',
            'educationalDirection'=> 'integer|exists:educationalDirections,id,status,1',
            'educationalLevel' => 'required|integer|exists:educationalLevels,id,status,1',
            'experienceLevel' => 'integer|exists:experienceLevels,id,status,1',
            'contractType' => 'required|integer|exists:contractTypes,id,status,1',
            'workingHours' => 'required|integer|exists:workingHours,id,status,1',
            'functionGroup' => 'integer|exists:functionGroups,id,status,1',
            'branches' => 'integer|exists:branches,id,status,1',
            'salaryMin' => 'numeric',
            'salaryMax' => 'numeric',
            'salaryPeriod' => 'integer|exists:salaryPeriods,id,status,1',
            'salaryCurrency' => 'integer|exists:salaryCurrencies,id,status,1',
            'organisationName' => 'required|string',
            'jobLocationCountry' => 'string|max:2',
            'jobLocationPostalcode' => 'required|string|max:10',
            'jobLocationCity' => 'string',
            'organisationWebsite' => 'string',
            'organisationLogo' => 'string',
            'recruiterName' => 'string',
            'recruiterEmail' => 'string|Email',
            'recruiterPhonenumber' => 'string',
            'jobBoard' => 'required|integer|exists:jobboards,id,status,1'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $customer = $this->getCustomerData($apiKey);
            if(isset($customer) && $customer->count() > 0 )
            {
                $customerId = $customer->toArray();
                $latLong = "";
                $latitudeLogitudeData = [];
                if((isset($input['jobLocationPostalcode']) && $input['jobLocationPostalcode'] != null) || (isset($input['jobLocationCity']) && $input['jobLocationCity'] != null)){
                    $pincode = isset($input['jobLocationPostalcode']) ? str_replace(' ', '', $input['jobLocationPostalcode']) : '-';
                    $city = isset($input['jobLocationCity']) ? $input['jobLocationCity'] : '-';

                    $latLong = $this->getLatLong($pincode,  $city);

                    preg_match('#\((.*?)\)#', $latLong, $match);

                    $latitude = '';
                    $longitude = '';
                    if (!empty($match)){
                        $latitudeLogitudeData = explode(' ', $match[1]);
                        $latitude = isset($latitudeLogitudeData) ? number_format($latitudeLogitudeData[0], 8) : null;
                        $longitude = isset($latitudeLogitudeData) ? number_format($latitudeLogitudeData[1], 8) : null;
                    }
                }

                $inputData = [
                    "customer_id" => isset($customerId[0]['id']) ? $customerId[0]['id'] : 1,
                    "job_id" => isset($input['jobId']) ? $input['jobId']:null,
                    "jobURL" => isset($input['jobURL']) ? $input['jobURL']:null,
                    "applicationURL" => isset($input['applicationURL']) ? $input['applicationURL']:null,
                    "functionTitle" => isset($input['functionTitle']) ? $input['functionTitle']:null,
                    "functionOneliner" => isset($input['functionOneliner']) ? $input['functionOneliner']:null,
                    "functionDescription" => isset($input['functionDescription']) ? $input['functionDescription']:null,
                    "educationalDirection" => isset($input['educationalDirection']) ? $input['educationalDirection']:null,
                    "educationalLevel" => isset($input['educationalLevel']) ? $input['educationalLevel']:null,
                    "experienceLevel" => isset($input['experienceLevel']) ? $input['experienceLevel']:null,
                    "contractType" => isset($input['contractType']) ? $input['contractType']:null,
                    "workingHours" => isset($input['workingHours']) ? $input['workingHours']:null,
                    "functionGroup" => isset($input['functionGroup']) ? $input['functionGroup']:null,
                    "branches" => isset($input['branches']) ? $input['branches']:null,
                    "salaryMin" => isset($input['salaryMin']) ? $input['salaryMin']:null,
                    "salaryMax" => isset($input['salaryMax']) ? $input['salaryMax']:null,
                    "salaryPeriod" => isset($input['salaryPeriod']) ? $input['salaryPeriod']:null,
                    "salaryCurrency" => isset($input['salaryCurrency']) ? $input['salaryCurrency']:null,
                    "jobLocationCountry" => isset($input['jobLocationCountry']) ? $input['jobLocationCountry']:null,
                    "jobLocationPostalcode" => isset($input['jobLocationPostalcode']) ? $input['jobLocationPostalcode']:null,
                    "jobLocationCity" => isset($input['jobLocationCity']) ? $input['jobLocationCity']:null,
                    "jobLocationLat" => $latitude ? $latitude : null,
                    "jobLocationLong" => $longitude ? $longitude : null,
                    "organisationName" => isset($input['organisationName']) ? $input['organisationName']:null,
                    "organisationWebsite" => isset($input['organisationWebsite']) ? $input['organisationWebsite']:null,
                    "organisationLogo" => isset($input['organisationLogo']) ? $input['organisationLogo']:null,
                    "recruiterName" => isset($input['recruiterName']) ? $input['recruiterName']:null,
                    "recruiterEmail" => isset($input['recruiterEmail']) ? $input['recruiterEmail']:null,
                    "recruiterPhonenumber" => isset($input['recruiterPhonenumber']) ? $input['recruiterPhonenumber']:null,
                    "jobBoard" => isset($input['jobBoard']) ? $input['jobBoard']:null,
                    "topJob" => isset($input['topJob']) && $input['topJob'] == true ? 1 : 0
                ];

                $job = job::create($inputData);

                if($job){
                    // $jobInfo = job::find($job->id);
                    DB::commit();
                    $status = 200;
                    $response['message'] = 'The job is created.';
                    $response['id'] = $job->id;
                }
            }
            else{
                DB::rollback();
                $status = 401;
                $response['message'] = 'Access token is missing or invalid.';
            }
        }catch (Exception $e) {
            DB::rollback();
            $response['message'] = 'Something went Wrong !!!';
            $status = 400;
        }

        return response()->json($response, $status);
    }


    /**
     * @OA\GET(
     *      path="/api/jobs/get/{jobId}",
     *      tags={"Jobs"},
     *      operationId="job.show",
     *
     *      summary="Retrieve a specific job",
     *      @OA\Parameter(
     *          name="api-key",
     *          in="header",
     *          description="bearer auth",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="jobId",
     *          in="path",
     *          description="the identifier of the job.",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="The job information.",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Access token is missing or invalid",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Job not found."
     *      ),
     *  )
     */
    public function getJobs(Request $request, $id){
        $apiKey = $request->header('api-key');
        $msg = [];
        $selectColumns = [
            // 'jobs.id',
            // 'jobs.date_created as datePosted',
            'jobs.job_id as jobId',
            'jobs.jobURL',
            'jobs.applicationURL',
            'jobs.functionTitle',
            'jobs.functionOneliner',
            'jobs.functionDescription',
            'jobs.educationalDirection',
            'jobs.educationalLevel',
            'jobs.experienceLevel',
            'jobs.contractType',
            'jobs.workingHours',
            'jobs.functionGroup',
            'jobs.branches',
            'jobs.salaryMin',
            'jobs.salaryMax',
            'jobs.salaryPeriod',
            'jobs.salaryCurrency',
            'jobs.jobLocationCountry',
            'jobs.jobLocationPostalcode',
            'jobs.jobLocationCity',
            'jobs.jobLocationLat',
            'jobs.jobLocationLong',
            'jobs.organisationName',
            'jobs.organisationWebsite',
            'jobs.organisationLogo',
            'jobs.recruiterName',
            'jobs.recruiterEmail',
            'jobs.recruiterPhonenumber',
            'jobs.jobBoard',
            'jobs.topJob'
        ];

        DB::beginTransaction();
        try {
                $customer = $this->getCustomerData($apiKey);

                if(isset($customer) && $customer->count() > 0 )
                {
                    if($id != null){
                        $customerId = $customer->toArray();

                        $jobInformation = job::select($selectColumns)->where('jobs.id', $id)->where('jobs.customer_id', $customerId[0]['id'])->where('status', '!=', 2)->get();

                        if(isset($jobInformation) && $jobInformation->count() > 0){
                            DB::commit();
                            $status = 200;
                            $msg['message'] = 'The job information.';
                            $jobInformation = $jobInformation->toarray()[0];
                            // $response = array_merge($msg, $jobInformation);
                            $response = $jobInformation;
                        }
                        else{
                            $status = 404;
                            $response['message'] = 'Job not found';
                        }
                    }else{
                        $status = 422;
                        $response['message'] = 'The job id field is required.';
                    }
                }
                else{
                    $status = 401;
                    $response['message'] = 'Access token is missing or invalid.';
                }
        } catch (Exception $e) {
            DB::rollback();
            $response['message'] = 'Something went Wrong !!!';
            $status = 400;
        }


        return response()->json($response, $status);
    }


    /**
     * @OA\PUT(
     *      path="/api/jobs/update/{jobId}",
     *      tags={"Jobs"},
     *      operationId="job.update",
     *
     *      summary="Update a specific job.",
     *      @OA\Parameter(
     *          name="api-key",
     *          in="header",
     *          description="bearer auth",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="jobId",
     *          in="path",
     *          description="the identifier of the job.",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="jobURL",
     *          in="query",
     *          description="the URL to the job.",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="applicationURL",
     *          in="query",
     *          description="the URL to the application form.",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="functionTitle",
     *          in="query",
     *          description="the title of the job",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="functionOneliner",
     *          in="query",
     *          description="the oneliner of the job",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="functionDescription",
     *          in="query",
     *          description="the description of the job.",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="educationalDirection",
     *          in="query",
     *          description="the desired education.",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="educationalLevel",
     *          in="query",
     *          description="the desired level of education.",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="experienceLevel",
     *          in="query",
     *          description="the desired level of experience.",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="contractType",
     *          in="query",
     *          description="the type of contract.",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="workingHours",
     *          in="query",
     *          description="the amount of working hours per week.",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="functionGroup",
     *          in="query",
     *          description="the functiongroup of the job.",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="branches",
     *          in="query",
     *          description="the branche the company is active.",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="salaryMin",
     *          in="query",
     *          description="the minimum salary.",
     *          required=false,
     *          @OA\Schema(
     *              type="number"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="salaryMax",
     *          in="query",
     *          description="the maximum salary.",
     *          required=false,
     *          @OA\Schema(
     *              type="number"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="salaryPeriod",
     *          in="query",
     *          description="the payment period of the salary.",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="salaryCurrency",
     *          in="query",
     *          description="the currency denomonation of the salary.",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="jobLocationCountry",
     *          in="query",
     *          description="the location of the job (country).",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="jobLocationPostalcode",
     *          in="query",
     *          description="the location of the job (postalcode).",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="jobLocationCity",
     *          in="query",
     *          description="the location of the job (city).",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="organisationName",
     *          in="query",
     *          description="the name of the organisation.",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="organisationWebsite",
     *          in="query",
     *          description="the website of the organisation.",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="organisationLogo",
     *          in="query",
     *          description="the logo of the organisation.",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="recruiterName",
     *          in="query",
     *          description="the name of the recruiter.",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="recruiterEmail",
     *          in="query",
     *          description="the emailaddress of the recruiter.",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="recruiterPhonenumber",
     *          in="query",
     *          description="the phonenumber of the recruiter.",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="jobBoard",
     *          in="query",
     *          description="the jobboard to post the job on.",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="topJob",
     *          in="query",
     *          required=false,
     *          @OA\Schema(
     *              type="boolean"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="The job is updated.",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Access token is missing or invalid",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Job not found."
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Job contains validation errors."
     *      ),
     *  )
     */
    public function updateJob(Request $request, $id){

        $input = $request->all();
        $apiKey = $request->header('api-key');

        // Validation
        $validator = Validator::make($request->all(), [
            'jobId' => 'integer',           // Job Primary key id
            'jobURL' => 'string',
            'applicationURL' => 'string',
            'functionTitle' => 'string',
            'functionOneliner' => 'string',
            'functionDescription' => 'string',
            'educationalDirection'=> 'integer|exists:educationalDirections,id,status,1',
            'educationalLevel' => 'integer|exists:educationalLevels,id,status,1',
            'experienceLevel' => 'integer|exists:experienceLevels,id,status,1',
            'contractType' => 'integer|exists:contractTypes,id,status,1',
            'workingHours' => 'integer|exists:workingHours,id,status,1',
            'functionGroup' => 'integer|exists:functionGroups,id,status,1',
            'branches' => 'integer|exists:branches,id,status,1',
            'salaryMin' => 'numeric',
            'salaryMax' => 'numeric',
            'salaryPeriod' => 'integer|exists:salaryPeriods,id,status,1',
            'salaryCurrency' => 'integer|exists:salaryCurrencies,id,status,1',
            'organisationName' => 'string',
            'jobLocationCountry' => 'string|max:2',
            'jobLocationPostalcode' => 'string|max:10',
            'jobLocationCity' => 'string',
            'organisationWebsite' => 'string',
            'organisationLogo' => 'string',
            'recruiterName' => 'string',
            'recruiterEmail' => 'string',
            'recruiterPhonenumber' => 'string',
            'jobBoard' => 'integer|exists:jobboards,id,status,1'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $customer = $this->getCustomerData($apiKey);
            if(isset($customer) && $customer->count() > 0 )
            {
                $customerId = $customer->toArray();

                $inputData = [
                    "customer_id" => isset($customerId[0]['id']) ? $customerId[0]['id'] : null,
                    "jobURL" => isset($input['jobURL']) ? $input['jobURL']:null,
                    "applicationURL" => isset($input['applicationURL']) ? $input['applicationURL']:null,
                    "functionTitle" => isset($input['functionTitle']) ? $input['functionTitle']:null,
                    "functionOneliner" => isset($input['functionOneliner']) ? $input['functionOneliner']:null,
                    "functionDescription" => isset($input['functionDescription']) ? $input['functionDescription']:null,
                    "educationalDirection" => isset($input['educationalDirection']) ? $input['educationalDirection']:null,
                    "educationalLevel" => isset($input['educationalLevel']) ? $input['educationalLevel']:null,
                    "experienceLevel" => isset($input['experienceLevel']) ? $input['experienceLevel']:null,
                    "contractType" => isset($input['contractType']) ? $input['contractType']:null,
                    "workingHours" => isset($input['workingHours']) ? $input['workingHours']:null,
                    "functionGroup" => isset($input['functionGroup']) ? $input['functionGroup']:null,
                    "branches" => isset($input['branches']) ? $input['branches']:null,
                    "salaryMin" => isset($input['salaryMin']) ? $input['salaryMin']:null,
                    "salaryMax" => isset($input['salaryMax']) ? $input['salaryMax']:null,
                    "salaryPeriod" => isset($input['salaryPeriod']) ? $input['salaryPeriod']:null,
                    "salaryCurrency" => isset($input['salaryCurrency']) ? $input['salaryCurrency']:null,
                    "jobLocationCountry" => isset($input['jobLocationCountry']) ? $input['jobLocationCountry']:null,
                    "jobLocationPostalcode" => isset($input['jobLocationPostalcode']) ? $input['jobLocationPostalcode']:null,
                    "jobLocationCity" => isset($input['jobLocationCity']) ? $input['jobLocationCity']:null,
                    "organisationName" => isset($input['organisationName']) ? $input['organisationName']:null,
                    "organisationWebsite" => isset($input['organisationWebsite']) ? $input['organisationWebsite']:null,
                    "organisationLogo" => isset($input['organisationLogo']) ? $input['organisationLogo']:null,
                    "recruiterName" => isset($input['recruiterName']) ? $input['recruiterName']:null,
                    "recruiterEmail" => isset($input['recruiterEmail']) ? $input['recruiterEmail']:null,
                    "recruiterPhonenumber" => isset($input['recruiterPhonenumber']) ? $input['recruiterPhonenumber']:null,
                    "jobBoard" => isset($input['jobBoard']) ? $input['jobBoard']:null,
                    "topJob" => isset($input['topJob']) && $input['topJob'] == true ? 1 : 0
                ];

                if($id != null){
                    $jobData = job::find($id);

                    if($jobData){
                        // update job

                        $latLong = "";
                        $latitudeLogitudeData = [];
                        if((isset($request->jobLocationPostalcode) && $request->jobLocationPostalcode != null) || (isset($request->jobLocationCity) && $request->jobLocationCity != null)){
                            $pincode = isset($request->jobLocationPostalcode) ? str_replace(' ', '', $request->jobLocationPostalcode) : '-';
                            $city = isset($request->jobLocationCity) ? $request->jobLocationCity : '-';

                            $latLong = $this->getLatLong($pincode,  $city);

                            preg_match('#\((.*?)\)#', $latLong, $match);

                            $latitude = '';
                            $longitude = '';
                            if (!empty($match)){
                                $latitudeLogitudeData = explode(' ', $match[1]);

                                $latitude = isset($latitudeLogitudeData) ? number_format($latitudeLogitudeData[0], 8) : null;
                                $longitude = isset($latitudeLogitudeData) ? number_format($latitudeLogitudeData[1], 8) : null;

                            }
                        }

                        $result = array_filter($request->all());

                        if($result != null){
                            if(isset($latitude) && isset($longitude)){
                                $jobUpdated = DB::table('jobs')->where('id', $id)->where('jobs.customer_id', $customerId[0]['id'])->where('status', '!=', '2')->update($result + ['jobLocationLat' => $latitude, 'jobLocationLong' => $longitude]);
                            }
                            else{
                                $jobUpdated = DB::table('jobs')->where('id', $id)->where('jobs.customer_id', $customerId[0]['id'])->where('status', '!=', '2')->update($result);
                            }

                            if($jobUpdated){
                                $jobs = job::find($id);
                                DB::commit();
                                $status = 200;
                                $response['message'] = 'The job has been updated.';
                                $response['job'] = $jobs;
                            }
                            else{
                                DB::rollback();
                                $status = 404;
                                $response['message'] = 'Job not found.';
                            }
                        }
                        else{
                            DB::rollback();
                            $status = 400;
                            $response['message'] = 'Please enter data to update.';
                        }
                    }
                    else{
                        DB::rollback();
                        $status = 404;
                        $response['message'] = 'Job not found.';
                    }
                }
                else{
                    DB::rollback();
                    $status = 422;
                    $response['message'] = 'The job id field is required.';
                }
            }
            else{
                DB::rollback();
                $status = 401;
                $response['message'] = 'Access token is missing or invalid.';
            }
        }catch (Exception $e) {
            DB::rollback();
            $response['message'] = 'Something went Wrong !!!';
            $status = 400;
        }

        return response()->json($response, $status);
    }

    /**
     * @OA\DELETE(
     *      path="/api/jobs/delete/{jobId}",
     *      tags={"Jobs"},
     *      operationId="job.delete",
     *
     *      summary="Delete a specific job.",
     *      @OA\Parameter(
     *          name="api-key",
     *          in="header",
     *          description="bearer auth",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="jobId",
     *          in="path",
     *          description="the identifier of the job.",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="The job has been deleted.",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Access token is missing or invalid",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Job not found."
     *      ),
     *  )
     */
    public function deleteJob(Request $request, $id){
        $apiKey = $request->header('api-key');


        DB::beginTransaction();
        try {
                $customer = $this->getCustomerData($apiKey);

                if(isset($customer) && $customer->count() > 0 )
                {
                    $customerId = $customer->toArray();
                    $updateData = [
                        'status' => '2'
                    ];

                    if($id != null){
                        $jobInformation = job::where('customer_id', $customerId[0]['id'])->where('status', '!=', '2')->find($id);
                        if(isset($jobInformation) && $jobInformation->count() > 0){
                            $jobDelete = DB::table('jobs')->where('id', $id)->update($updateData);

                            if(isset($jobDelete)){
                                DB::commit();
                                $status = 200;
                                $response['message'] = 'The job has been deleted';
                            }
                            else{
                                DB::rollback();
                                $status = 400;
                                $response['message'] = 'Something went Wrong ! Job has been not deleted.';
                            }
                        }
                        else{
                            $status = 404;
                            $response['message'] = 'Job not found';
                        }
                    }
                    else{
                        $status = 422;
                        $response['message'] = 'The job id field is required.';
                    }
                }
                else{
                    $status = 401;
                    $response['message'] = 'Access token is missing or invalid.';
                }
        } catch (Exception $e) {
            DB::rollback();
            $response['message'] = 'Something went Wrong !!!';
            $status = 400;
        }
        return response()->json($response, $status);
    }


    /**
     * @OA\GET(
     *      path="/api/jobs/jobList",
     *      tags={"Jobs"},
     *      operationId="organisations.getJobs",
     *
     *      summary="Retrieve a list of active jobs",
     *      @OA\Parameter(
     *          name="api-key",
     *          in="header",
     *          description="bearer auth",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="A list of active jobs",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Access token is missing or invalid",
     *      ),
     *  )
     */
    public function getJobList(Request $request){
        $apiKey = $request->header('api-key');
        DB::beginTransaction();
        try {
                $customer = $this->getCustomerData($apiKey);
                if(isset($customer) && $customer->count() > 0 )
                {
                    $customerId = $customer->toArray();
                    $selectColumns = [
                        'id',
                        DB::raw("DATE_FORMAT(date_created,'%d/%m/%Y') as datePosted"),
                        'job_id as jobId',
                        'functionTitle'
                    ];

                    $jobInformation = job::select($selectColumns)->where('customer_id', $customerId[0]['id'])->where('status', '=', '1')->get();
                    if(isset($jobInformation) && $jobInformation->count() > 0){
                        DB::commit();
                        $status = 200;
                        $response['message'] = 'A list of active jobs.';
                        $response['items'] = $jobInformation;
                    }
                    else{
                        $status = 404;
                        $response['message'] = 'Job not found';
                    }
                }
                else{
                    $status = 401;
                    $response['message'] = 'Access token is missing or invalid.';
                }
        } catch (Exception $e) {
            DB::rollback();
            $response['message'] = 'Something went Wrong !!!';
            $status = 400;
        }
        return response()->json($response, $status);
    }



    /**
     * @OA\GET(
     *      path="/api/jobs/valueList/{listName}",
     *      tags={"Jobs"},
     *      operationId="value.getList",
     *
     *      summary="Retrieve list of active value.",
     *      @OA\Parameter(
     *          name="api-key",
     *          in="header",
     *          description="bearer auth",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="listName",
     *          in="path",
     *          description="the name of the required value list",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description=" All list of active value",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Access token is missing or invalid",
     *      ),
     *  )
     */
    public function getValueList(Request $request, $valueList){
        $apiKey = $request->header('api-key');
        DB::beginTransaction();
        try {
                $customer = $this->getCustomerData($apiKey);
                if(isset($customer) && $customer->count() > 0 )
                {
                    $customerId = $customer->toArray();

                    $valueListArray = ['branches','contractTypes', 'educationalDirections', 'educationalLevels', 'experienceLevels', 'functionGroups', 'jobBoards', 'salaryCurrencies', 'salaryPeriods', 'workingHours'];

                    if($valueList != null){
                        if(in_array($valueList, $valueListArray))
                        {
                            $jobInformation = $this->getJobReferenceTableDetails($valueList);
                            $response['message'] = 'A list of active jobs.';
                            // $response = array_merge($msg, $jobInformation);
                            $response[$valueList] = $jobInformation;
                            $status = 200;
                        }
                        else{
                            $status = 422;
                            $response['message'] = 'Please enter valid list name.';
                        }

                    } else{
                            $status = 422;
                            $response['message'] = 'The list name field is required.';
                        }
                }
                else{
                    $status = 401;
                    $response['message'] = 'Access token is missing or invalid.';
                }
        } catch (Exception $e) {
            DB::rollback();
            $response['message'] = 'Something went Wrong !!!';
            $status = 400;
        }
        return response()->json($response, $status);
    }

    /**
     * Get Customer Data Using Api Key
     */
    public function getCustomerData($apiKey){
        $customerData = Customer::where('api_key', $apiKey)->get();

        return  $customerData;
    }


    public function getJobReferenceTableDetails($table)
    {
        $result = DB::table($table)->select('id', 'description')->where('status', 1)->get()->toArray();
        return $result;
    }


    /**
     * Get Two fileds form third party api
     * jobLocationLat,jobLocationLong
     * city for type:woonplaats
     * pincode for type:postcode
     */
    public function getLatLong($pincode=null, $city=null)
    {

        # code...
        $q = "";
        $t = "";
        if(isset($pincode) && $pincode != '-'){
            $q = $pincode;
            $t = 'postcode'; // postalcode
        }else{
            $q = $city;
            $t = 'woonplaats';
        }

        $client = new Client();
        $response = $client->request('GET', "https://geodata.nationaalgeoregister.nl/locatieserver/v3/free?q='+$q+'&type:'+$t+'");
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $body = json_decode($body, true);
        if($body['response']['numFound'] > 0 && $statusCode == '200'){
            $result = $body['response']['docs'][0]['centroide_ll'];
            return($result);
        }
        else{
            return 'Not Valid.';
        }
    }
}
