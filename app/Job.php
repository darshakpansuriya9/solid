<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{

    protected $table='jobs';

    protected $fillable = ['customer_id', 'job_id', 'jobURL', 'applicationURL', 'functionTitle', 'functionOneliner', 'functionDescription','educationalDirection','educationalLevel','experienceLevel','contractType','workingHours','functionGroup','branches','salaryMin','salaryMax','salaryPeriod','salaryCurrency','organisationName','jobLocationCountry','jobLocationPostalcode', 'jobLocationCity', 'jobLocationLat', 'jobLocationLong', 'organisationWebsite','organisationLogo', 'recruiterName', 'recruiterEmail','recruiterPhonenumber', 'jobBoard'];

    public $timestamps = false;

    /**
     * Get the Category record associated with the Quote.
    */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'category_id');
    }

    /*
     * @name GetJoinEducationalDirection
     * @description get join with educational direction
     * @return Query
     */
    public function scopeGetJoinEducationalDirection($q){
        $q->join("educationaldirections","educationaldirections.id","=","jobs.educationalDirection");
    }

    /*
     * @name getJoinEducationalLevel
     * @description get join with educational direction
     * @return Query
     */
    public function scopeGetJoinEducationalLevel($q){
        $q->join("educationallevels","educationallevels.id","=","jobs.educationalLevel");
    }


    /*
     * @name getJoinExperienceLevel
     * @description get join with educational direction
     * @return Query
     */
    public function scopeGetJoinExperienceLevel($q){
        $q->join("experiencelevels","experiencelevels.id","=","jobs.experienceLevel");
    }


    /*
     * @name getJoinContractType
     * @description get join with educational direction
     * @return Query
     */
    public function scopeGetJoinContractType($q){
        $q->join("contracttypes","contracttypes.id","=","jobs.contractType");
    }


    /*
     * @name getJoinWorkingHour
     * @description get join with educational direction
     * @return Query
     */
    public function scopeGetJoinWorkingHour($q){
        $q->join("workinghours","workinghours.id","=","jobs.workingHours");
    }


    /*
     * @name getJoinFunctionGroup
     * @description get join with educational direction
     * @return Query
     */
    public function scopeGetJoinFunctionGroup($q){
        $q->join("functiongroups","functiongroups.id","=","jobs.functionGroup");
    }


    /*
     * @name getJoinBranche
     * @description get join with educational direction
     * @return Query
     */
    public function scopeGetJoinBranche($q){
        $q->join("branches","branches.id","=","jobs.branches");
    }


    /*
     * @name getJoinSalaryPeriod
     * @description get join with educational direction
     * @return Query
     */
    public function scopeGetJoinSalaryPeriod($q){
        $q->join("salaryperiods","salaryperiods.id","=","jobs.salaryPeriod");
    }



    /*
     * @name getJoinSalaryCurrency
     * @description get join with educational direction
     * @return Query
     */
    public function scopeGetJoinSalaryCurrency($q){
        $q->join("salarycurrencies","salarycurrencies.id","=","jobs.salaryCurrency");
    }


    /*
     * @name getJoinJobBoard
     * @description get join with educational direction
     * @return Query
     */
    public function scopeGetJoinJobBoard($q){
        $q->join("jobboards","jobboards.id","=","jobs.jobBoard");
    }
}
