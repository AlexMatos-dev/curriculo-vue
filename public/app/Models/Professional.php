<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Professional extends Model
{
    protected $primaryKey = 'professional_id';
    protected $table = 'professionals';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'person_id',
        'professional_slug',
        'professional_firstname',
        'professional_lastname',
        'professional_email',
        'professional_phone',
        'professional_photo',
        'professional_cover',
        'professional_title',
        'currently_working',
        'avaliable_to_travel',
        'paying'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id')->first();
    }

    /**
     * Creates or Updates $this Professional by sent data
     * @param Array - Schema [$attrName => $attrValue]
     * @return Profession|False
     */
    public function saveProfessional($data = [])
    {
        $myAttributes = $this->fillable;
        foreach($data as $attr => $value){
            if(in_array($attr, $myAttributes))
                $this->{$attr} = $value;
        }
        if(!$this->save())
            return false;
        return $this;
    }

    /**
     * Gets this Professional DataPerson object, if it exists
     * @return DataPerson|Null
     */
    public function getDataPerson()
    {
        return DataPerson::where('dpprofes_id', $this->professional_id)->first();
    }

    /**
     * Tries to add a job modality to logged professional
     * @param Int jobModalityId
     * @return Bool
     */
    public function syncJobModalities($jobModalityId = null)
    {
        if(!$jobModalityId || $this->isMyJobModality($jobModalityId))
            return false;
        return ProfessionalJobModality::create([
            'professional_id' => $this->professional_id,
            'job_modality_id' => $jobModalityId 
        ]);
    }

    /**
     * Tries to remove a job modality from professional
     * @param Int jobModalityId
     * @return Bool
     */
    public function removeJobModality($jobModalityId = null)
    {
        $jobModality = $this->isMyJobModality($jobModalityId);
        if(!$jobModalityId || $jobModality)
            return false;
        return $jobModality->delete();
    }

    /**
     * Checks if logged professional already posses sent jobModalityId
     * @param Int jobModalityId
     * @return ProfessionalJobModality
     */
    public function isMyJobModality($jobModalityId = null)
    {
        $jobModalitiesArray = $this->getJobModalities();
        return array_key_exists($jobModalityId, $jobModalitiesArray) ? $jobModalitiesArray[$jobModalityId] : false;
    }

    /**
     * Gets all job modalities of logged professional
     * @param Int jobModalityId
     * @return Array - of ProfessionalJobModality objects
     */
    public function getJobModalities()
    {
        $objectArray = ProfessionalJobModality::leftJoin('job_modalities AS jobModality', function($join){
            $join->on('professionals_job_modalities.professional_job_modality_id', '=', 'jobModality.job_modality_id')
                ->where('professionals_job_modalities.professional_id', $this->professional_id);
        })->get();
        $jobModalitiesArray = [];
        foreach($objectArray as $object){
            $jobModalitiesArray[$object->job_modality_id] = $object;
        }
        return $jobModalitiesArray;
    }
}