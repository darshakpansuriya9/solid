<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api', 'prefix' => 'jobs'], function(){
    Route::get('get/{jobId}', 'JobController@getJobs');
    Route::post('create/', 'JobController@createJob');
    Route::put('update/{jobId}', 'JobController@updateJob');
    Route::delete('delete/{jobId}', 'JobController@deleteJob');
    Route::get('jobList', 'JobController@getJobList');
    Route::get('valueList/{listName}', 'JobController@getValueList');
    Route::get('getLatLong', 'JobController@getLatLong');
});
