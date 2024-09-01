<?php

namespace App\Models;

use App\Helpers\ModelUtils;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Professional extends Model
{
    use SoftDeletes;

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
        'paying',
        'professional_ddi'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id')->first();
    }

    public function getFullName()
    {
        return ucwords($this->professional_firstname) . ' ' .  ucwords($this->professional_lastname);
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
     * @param Bool idAsIndex
     * @return Array - of ProfessionalJobModality objects
     */
    public function getJobModalities($idAsIndex = true)
    {
        $objectArray = ProfessionalJobModality::where('professional_id', $this->professional_id)->leftJoin('job_modalities AS jobModality', function($join){
            $join->on('professionals_job_modalities.professional_job_modality_id', '=', 'jobModality.job_modality_id');
        })->get();
        $jobModalitiesArray = [];
        foreach($objectArray as $object){
            if($idAsIndex){
                $jobModalitiesArray[$object->job_modality_id] = $object;
            }else{
                $jobModalitiesArray[] = $object;
            }
        }
        return $jobModalitiesArray;
    }

    /**
     * Tries to add a job modality to logged professional
     * @param Int lprofession_id
     * @return Bool
     */
    public function syncProfessionalProfessions($lprofession_id = null, $started_working_at = null, $observations = null)
    {
        if(!$lprofession_id || $this->isMyProfession($lprofession_id))
            return false;
        return ProfessionalProfession::create([
            'professional_id' => $this->professional_id,
            'lprofession_id' => $lprofession_id,
            'started_working_at' => $started_working_at,
            'observations' => $observations
        ]);
    }

    /**
     * Tries to remove a professional profession from professional
     * @param Int lprofession_id
     * @return Bool
     */
    public function removeProfessionProfession($lprofession_id = null)
    {
        $professionProfecion = $this->isMyProfession($lprofession_id);
        if(!$lprofession_id || $professionProfecion)
            return false;
        return $professionProfecion->delete();
    }

    /**
     * Checks if logged professional already posses sent lprofession_id
     * @param Int lprofession_id
     * @return ProfessionalProfession
     */
    public function isMyProfession($lprofession_id = null)
    {
        $jobModalitiesArray = $this->getProfessionProfessions();
        return array_key_exists($lprofession_id, $jobModalitiesArray) ? $jobModalitiesArray[$lprofession_id] : false;
    }

    /**
     * Gets all profession professions of logged professional
     * @param Bool idAsIndex
     * @return Array - of ProfessionalProfession objects
     */
    public function getProfessionProfessions($idAsIndex = true)
    {
        $objectArray = ProfessionalProfession::where('professional_id', $this->professional_id)->leftJoin('listprofessions', function($join){
            $join->on('professionals_professions.lprofession_id', '=', 'listprofessions.lprofession_id');
        })->get();
        $professionalProfessionsArray = [];
        foreach($objectArray as $object){
            if($idAsIndex){
                $professionalProfessionsArray[$object->professional_profession_id] = $object;
            }else{
                $professionalProfessionsArray[] = $object;
            }
        }
        return $professionalProfessionsArray;
    }

    /**
     * Lists professionals, filtering rsults accordingly to sent parameters
     * @param Illuminate\Http\Request $request - schema: [
     *      'skill_name' => Array, 'skproficiency_level' => Array 
     *      'area_of_study' => Array, 'certification_name' => String, 'exjob_title' => String, 'dpgender' => Int,
     *      'dpcity_id' => Int, 'dpstate_id' => Int, 'dpcountry_id' => Int,
     *      'lalangue_id' => Array, 'laspeaking_level' => Int, 'lalistening_level' => Int, 'lawriting_level' => Int, 'lareading_level' => Int
     *      'visa_type' => Array, 'vicountry_id' => Int, 'professional_id' => Int
     * ]
     * @param Bool paying
     * @param Int limit
     * @param Int offset
     * @return Array
     */
    public function listProfessionals(\Illuminate\Http\Request $request, $paying = false, $limit = 10000, $offset = null)
    {
        $proficiencyObj = new Proficiency();
        $requestParamArray = $request->all();
        $requestKeysArray = array_keys($requestParamArray);
        $limit = !is_numeric($limit) ? 10000 : $limit;
        $limit = $limit > 10000 ? 10000 : $limit;
        $limit = $limit < 0 ? 1 : $limit;

        $queryParametersFinal = [];

        $query = Professional::where('professionals.paying', $paying)->leftJoin('persons', function($join){
            $join->on('persons.person_id', '=', 'professionals.person_id');
        })->leftJoin('curriculums', function($join){
            $join->on('professionals.professional_id', '=', 'curriculums.cprofes_id');
        });
        if($request->has('professional_id')){
            $query->where('professionals.professional_id', $request->professional_id);
        }
        $sizeOfParameters = 1;
        $orderBy = [];
        $skills_params = ['skproficiency_level'];
        if($request->has('skill_name') && is_array($request->skill_name)){
            $query->leftJoin('skills', function($join){
                $join->on('curriculums.curriculum_id', '=', 'skills.skcurriculum_id');
            });
            $index = 0;
            $skillsProfeciencies = $proficiencyObj->getProficiencies(Proficiency::CATEGORY_LEVEL, true);
            foreach($request->skill_name as $skillId){
                $params = [];
                $params[] = ['skills.skill_name', $skillId];
                foreach($skills_params as $skillData){
                    if(!$request->has($skillData) || !$request->{$skillData} || empty($request->{$skillData}[$index]) || !in_array($request->{$skillData}[$index], $skillsProfeciencies))
                        continue;
                    $params[] = ["skills.$skillData", $request->{$skillData}[$index]];
                }
                $queryParametersFinal[] = $params;
                $orderBy[] = "skills.updated_at";
                $index++;
            }
            $sizeOfParameters = $sizeOfParameters < $index ? $index : $sizeOfParameters;
        }
        if($request->has('area_of_study') && count($request->area_of_study) > 0){
            $query->leftJoin('educations', function($join) use($request){
                $join->on('curriculums.curriculum_id', '=', 'educations.edcurriculum_id');
                foreach($request->area_of_study as $areaOfStudyId){
                    $join->where('educations.edfield_of_study', $areaOfStudyId);
                }
            });
            $orderBy[] = 'educations.updated_at';
            $educationsSize = count($request->area_of_study);
            $sizeOfParameters = $sizeOfParameters < $educationsSize ? $educationsSize : $sizeOfParameters;
        }
        if($request->has('certification_name')){
            $query->leftJoin('certifications', function($join){
                $join->on('curriculums.curriculum_id', '=', 'certifications.cercurriculum_id');
            })->where('certifications.certification_name', 'like', '%'.$request->certification_name.'%');
        }
        if($request->has('exjob_title')){
            $query->leftJoin('experiences', function($join){
                $join->on('curriculums.curriculum_id', '=', 'experiences.excurriculum_id');
            })->where('experiences.exjob_title', 'like', '%'.$request->exjob_title.'%');
        }
        $dataPersonParam = ['dpgender', 'dpcity_id', 'dpstate_id', 'dpcountry_id'];
        if(count(array_diff($dataPersonParam, $requestKeysArray)) < count($dataPersonParam)){
            $params = [];
            $index = 0;
            $query->leftJoin('dataperson', function($join){
                $join->on('professionals.professional_id', '=', 'dataperson.dpprofes_id');
            });
            foreach($dataPersonParam as $dtPersonParam){
                if(!$request->has($dtPersonParam) || !$request->{$dtPersonParam} || empty($request->{$dtPersonParam}[$index]))
                    continue;
                $params[] = ["dataperson.$dtPersonParam", $request->{$dtPersonParam}[$index]];
                $index++;
            }
            $queryParametersFinal[] = $params;
            $orderBy[] = "dataperson.updated_at";
            $sizeOfParameters = $sizeOfParameters < $index ? $index : $sizeOfParameters;
        }
        $languageParam = ['laspeaking_level', 'lalistening_level', 'lawriting_level', 'lareading_level'];
        if($request->has('lalangue_id') && is_array($request->lalangue_id)){
            $query->leftJoin('languages', function($join){
                $join->on('curriculums.curriculum_id', '=', 'languages.lacurriculum_id');
            });
            $index = 0;
            $languageProficiencies = $proficiencyObj->getProficiencies(Proficiency::CATEGORY_LANGUAGE, true);
            foreach($request->lalangue_id as $langId){
                $params = [];
                $params[] = ['languages.lalangue_id', $langId];
                foreach($languageParam as $langData){
                    if(!$request->has($langData) || !$request->{$langData} || empty($request->{$langData}[$index]) || !in_array($request->{$langData}[$index], $languageProficiencies))
                        continue;
                    
                    $params[] = ["languages.$langData", $request->{$langData}[$index]];
                }
                $queryParametersFinal[] = $params;
                $orderBy[] = "languages.updated_at";
                $index++;
            }
            $sizeOfParameters = $sizeOfParameters < $index ? $index : $sizeOfParameters;
        }
        $visaParam = ['vicountry_id'];
        if($request->has('visa_type') && is_array($request->visa_type)){
            $query->leftJoin('visas', function($join){
                $join->on('curriculums.curriculum_id', '=', 'visas.vicurriculum_id');
            });
            $index = 0;
            foreach($request->visa_type as $visaTypeId){
                $params = [];
                $params[] = ['visas.visa_type', $visaTypeId];
                foreach($visaParam as $visaData){
                    if(!$request->has($visaData) || !$request->{$visaData} || empty($request->{$visaData}[$index]))
                        continue;
                    $params[] = ["visas.$visaData", $request->{$visaData}[$index]];
                }
                $queryParametersFinal[] = $params;
                $orderBy[] = "visas.updated_at";
                $index++;
            }
            $sizeOfParameters = $sizeOfParameters < $index ? $index : $sizeOfParameters;
        }
        // Setting query
        $query->where(function($query) use ($queryParametersFinal) {
            foreach($queryParametersFinal as $parameters){
                $query->orWhere(function($query) use ($parameters){
                    $query->orWhere($parameters);
                });
            }
        });
        $query->orderBy('persons.last_login', 'DESC');
        foreach($orderBy as $attrToOrder){
            $query->orderBy($attrToOrder, 'DESC');
        }
        $sizeOfLimit = $limit * $sizeOfParameters;
        if($offset)
            $query->offset($offset);
        $query->limit($sizeOfLimit);
        return $query->get();
    }

    public function splitJoinDataFromListedProfessions($professionalArray = [], $request = false)
    {
        $ids = [];
        foreach($professionalArray as $professional){
            if(in_array($professional->professional_id, $ids))
                continue;
            $ids[] = $professional->professional_id;
        }
        $objectsAttrsArray = [
            'skills' => ['id' => 'skill_id', 'data' => ['skcurriculum_id', 'skill_name', 'skproficiency_level']],
            'languages'  => ['id' => 'lang_id', 'data' => ['lacurriculum_id', 'lalangue_id', 'laspeaking_level', 'lalistening_level', 'lawriting_level', 'lareading_level']],
            'visas'  => ['id' => 'visas_id', 'data' => ['vicurriculum_id', 'vicountry_id', 'visa_type']],
            'areaOfStudies' => ['id' => 'area_of_study_id', 'data' => ['edcurriculum_id', 'eddegree', 'degree_type', 'edfield_of_study', 'edinstitution', 'edstart_date', 'edend_date', 'eddescription']],
        ];
        $allData = Professional::whereIn('professionals.professional_id', $ids)->leftJoin('curriculums', function($join){
            $join->on('curriculums.cprofes_id', '=', 'professionals.professional_id');
        })->leftJoin('skills', function($join){
            $join->on('curriculums.curriculum_id', '=', 'skills.skcurriculum_id');
        })->leftJoin('visas', function($join){
            $join->on('curriculums.curriculum_id', '=', 'visas.vicurriculum_id');
        })->leftJoin('languages', function($join){
            $join->on('curriculums.curriculum_id', '=', 'languages.lacurriculum_id');
        })->get();

        $tags = ModelUtils::getIdIndexedAndTranslated(new Tag(), 'tags_name');
        $proficiencies = ModelUtils::getIdIndexedAndTranslated(new Proficiency(), 'proficiency_level');
        $visaTypes = ModelUtils::getIdIndexedAndTranslated(new TypeVisas(), 'type_name');
        $listLanguages = ModelUtils::getIdIndexedAndTranslated(new ListLangue(), 'llangue_name');
        $listCountry = ModelUtils::getIdIndexedAndTranslated(new ListCountry(), 'lcountry_name');

        $filteredProfessionalData = [];
        foreach($allData as $professionalData){
            $filteredProfessionalData[$professionalData->professional_id][] = $professionalData;
        }
        $results = [];
        $insert = [
            'skills'    => Skill::class, 
            'visas'     => Visa::class, 
            'languages' => Language::class
        ];
        $instances = [
            'skills' => ['object' => Skill::class, 'data' => [
                'skproficiency_level' => ['objects' => $proficiencies, 'translated' => true, 'to' => 'tag'],
                'skill_name' => ['objects' => $tags, 'translated' => true, 'to' => 'language']
            ]],
            'visas' => ['object' => Visa::class, 'data' => [
                'visa_type' => ['objects' => $visaTypes, 'translated' => true, 'to' => 'visa_type'],
                'vicountry_id' => ['objects' => $listCountry, 'translated' => true, 'to' => 'country']
            ]],
            'languages' => ['object' => Language::class, 'data' => [
                'lalangue_id' => ['objects' => $listLanguages, 'translated' => true, 'to' => 'language'],
                'laspeaking_level' => ['objects' => $proficiencies, 'translated' => true, 'to' => 'speaking_level'],
                'lalistening_level' => ['objects' => $proficiencies, 'translated' => true, 'to' => 'listening_level'],
                'lawriting_level' => ['objects' => $proficiencies, 'translated' => true, 'to' => 'writting_level'],
                'lareading_level' => ['objects' => $proficiencies, 'translated' => true, 'to' => 'reading_level']
            ]]
        ];
        foreach($professionalArray as $professional){
            foreach($insert as $type => $object){
                $gathered = [
                    'skills'    => [],
                    'visas'     => [],
                    'languages' => []
                ];
                $usedIds = [
                    'skills'    => [],
                    'visas'     => [],
                    'languages' => []
                ];
                foreach($filteredProfessionalData[$professional->professional_id] as $professionalData){
                    $id = $professionalData->{$objectsAttrsArray[$type]['id']};
                    if(in_array($id, $usedIds[$type]) || !$id)
                        continue;
                    $usedIds[$type][] = $id;
                    $objectInstaceAsArray[$objectsAttrsArray[$type]['id']] = $id;
                    foreach($objectsAttrsArray[$type]['data'] as $attr){
                        $objectInstaceAsArray[$attr] = $professionalData->{$attr};
                    }
                    $objName = $instances[$type]['object'];
                    $newInstaceOfObject = new $objName($objectInstaceAsArray);
                    if(!$newInstaceOfObject->{$newInstaceOfObject->getKeyName()})
                        $newInstaceOfObject->{$newInstaceOfObject->getKeyName()} = $professionalData->{$newInstaceOfObject->getKeyName()};
                    $values = ModelUtils::getFillableData($newInstaceOfObject, true, $instances[$type]['data']);

                    $gathered[$type][] = $values;
                }
                $professional->{$type} = $gathered[$type];
            }
            if($request)
                $professional->match = $this->generateCompatilityMatchOfProfessional($professional, $request);
            $results[] = $professional;
        }
        return $results;
    }

    /**
     * Reads sent job list data to order jobs
     * @param Array data - Schema: ['paying' => [], 'nonPaying' => []]
     * @param Int maxJobs - default: 100: Max results to return
     * @param Int notPayingCoeficient - default 5: Paying jobs until one nonPaying job to be added to list
     * @return Array ['results' => [], 'status' => ['paying' => 0, 'nonPaying' => 0]]
     */
    public function processListedProfessions($data = [], $maxProfessionals = 100, $notPayingCoeficient = 5)
    {
        $results = [];
        $tracker = 1;
        $totalProfessionals = 0;
        $notPayingIndex = 0;
        $status = [
            'paying' => 0,
            'nonPaying' => 0
        ];
        $paying = $data['paying'];
        $nonPaying = $data['nonPaying'];
        if(empty($paying)){
            $results = $nonPaying;
        }else if(count($paying) < $notPayingCoeficient){
            $results = $paying;
            $index = count($paying);
            foreach($nonPaying as $notPayingProfessionals){
                if($index == $maxProfessionals)
                    break;
                $results[] = $notPayingProfessionals;
                $index++;
            }
        }else{
            foreach($paying as $payingProfessionals){
                if($tracker == $notPayingCoeficient){
                    if($totalProfessionals == $maxProfessionals)
                        break;
                    $tracker = 1;
                    $nonPayingProfessionals = !empty($nonPaying[$notPayingIndex]) ? $nonPaying[$notPayingIndex] : null;
                    if($nonPayingProfessionals){
                        $results[] = $nonPayingProfessionals;
                        $notPayingIndex++;
                        $totalProfessionals++;
                        $status['nonPaying']++;
                    }
                }
                if($totalProfessionals == $maxProfessionals)
                    break;
                $results[] = $payingProfessionals;
                $totalProfessionals++;
                $tracker++;
                $status['paying']++;
            }
        }
        if($results < $maxProfessionals){
            foreach($nonPaying as $nonPayingProfessionals){
                if($totalProfessionals == $maxProfessionals)
                    break;
                $results[] = $nonPayingProfessionals;
                $totalProfessionals++;
            }
        }
        return [
            'results' => $results,
            'status'  => $status
        ];
    }

    /**
     * Returns a number wiht the match of this Professional by Request parametes
     * @param Professional $professional
     * @param \Illuminate\Http\Request $parameters
     * @return Float|Null
     */
    public function generateCompatilityMatchOfProfessional($professional, $parameters = null)
    {
        if(!$parameters || !is_object($parameters))
            return null;
        $validParameters = [
            'equal'   => [],
            'like'    => ['certification_name', 'exjob_title'],
            'in'      => [],
            'inArray' => ['dpgender', 'dpcity_id', 'dpstate_id', 'dpcountry_id'],
            'many'    => [
                'skill_name::skills', 'skproficiency_level:skills', 'typevisas_id::visas', 'vicountry_id::visas', 
                'lalangue_id::languages', 'laspeaking_level::languages', 'lalistening_level::languages', 'lawriting_level::languages', 'lareading_level::languages'
            ]
        ];
        $match = 0;
        $totalKeys = 0;
        // Gather all parameters
        foreach($validParameters as $checkType => $keys){
            foreach($keys as $key){
                if($checkType == 'many'){
                    $data = explode('::', $key);
                    $keyName = $data[0];
                    $keyId = count($data) == 2 ? $data[1] : null;
                    $keyValue = $parameters->has($keyName) ? $parameters->{$keyName} : null; 
                }else{
                    $keyValue = $parameters->has($key) ? $parameters->{$key} : null;
                }
                if(!$keyValue)
                    continue;
                $totalKeys++;
            }
        }
        $matchCoeficient = $totalKeys > 0 ? $matchCoeficient = 100 / $totalKeys : 100;
        // Read values
        foreach($validParameters as $checkType => $keys){
            foreach($keys as $key){
                if($checkType == 'many'){
                    $data = explode('::', $key);
                    $keyName = $data[0];
                    $keyId = count($data) == 2 ? $data[1] : null;
                    $keyValue = $parameters->has($keyName) ? $parameters->{$keyName} : null; 
                }else{
                    $keyValue = $parameters->has($key) ? $parameters->{$key} : null;
                }
                if(!$keyValue)
                    continue;
                switch($checkType){
                    case 'equal':
                        if($keyValue == $professional->{$key})
                            $match = $match + $matchCoeficient;
                    break;
                    case 'like':
                        if(is_numeric(strpos($professional->{$key}, $keyValue)))
                            $match = $match + $matchCoeficient;
                    break;
                    case 'in':
                        $dataArray = explode('::', $keyValue);
                        if(count($dataArray) != 2)
                            break;
                        $values = explode('|', $dataArray[1]);
                        if(count($values) != 2)
                            break;
                        $attr = $dataArray[0];
                        $fromValue = $values[0];
                        $toValue   = $values[1];
                        if(!$fromValue || !$toValue)
                            $match = $match + ($matchCoeficient / 2);
                        if($fromValue >= $professional->{$attr} && $toValue <= $professional->{$attr})
                            $match = $match + ($matchCoeficient / 2);
                    break;
                    case 'many':
                        if(!is_array($keyValue) || count($keyValue) < 1)
                            break;
                        $valid = [];
                        $objectValue = $professional->{$keyId};
                        foreach($objectValue as $valObj){
                            if(in_array($valObj->{$keyName}, $keyValue) && !in_array($valObj->{$keyName}, $valid))
                                $valid[] = $valObj->{$keyName};
                        }
                        $totalSize = count($keyValue);
                        $thisVal = ($matchCoeficient / $totalSize) * count($valid);
                        $match = $match + $thisVal;
                    break;
                    case 'inArray':
                        $objectValue = $professional->{$key};
                        $thisVal = $matchCoeficient / count($keyValue);
                        if(in_array($objectValue, $keyValue)){
                            $match = $match + $thisVal;
                        }
                    break;
                }
            }
        }
        $matchValue = number_format((float)$match, 2, '.', '');
        $matchValue = $matchValue > 100 ? 100 : $matchValue;
        return $matchValue < 0 ? 0 : $matchValue;
    }

    /**
     * Gather $this professional skills, visas and languages
     * @return Professional
     */
    public function gatherInformation()
    {
        $result = $this->splitJoinDataFromListedProfessions([$this]);
        return !empty($result) ? $result[0] : $this;
    }
}