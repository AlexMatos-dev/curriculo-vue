<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;

class Education extends Model
{
    protected $primaryKey = 'education_id';
    protected $table = 'educations';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'edcurriculum_id',
        'eddegree',
        'degree_type',
        'edfield_of_study',
        'edinstitution',
        'edstart_date',
        'edend_date',
        'eddescription'
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'edcurriculum_id');
    }

    public function degreeType()
    {
        return $this->belongsTo(DegreeType::class, 'degree_type');
    }

    public function areaOfStudy()
    {
        return $this->belongsTo(AreaOfStudy::class, 'edfield_of_study');
    }

    /**
     * Fetches all educations form sent curriculum id with all realted data and translations
     * @param Int curriculum_id
     * @param Int per_page - default = 10
     * @param Int education_id - if sent will return only one result
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function list($curriculum_id, $per_page = 10, $education_id = null)
    {
        $queryObj = Education::select('educations.*', 'areas_of_study.*', 'degree_types.*', 'areas_of_study_translation.en AS area_of_study_en',
        'areas_of_study_translation.pt AS area_of_study_pt', 'areas_of_study_translation.es AS area_of_study_es',
        'degree_types_translation.en AS degree_type_en', 'degree_types_translation.pt AS degree_type_pt',
        'degree_types_translation.es AS degree_type_es')->where('edcurriculum_id', $curriculum_id)
        ->leftJoin('areas_of_study', function($join){
            $join->on('areas_of_study.area_of_study_id', '=', 'educations.edfield_of_study');
        })->leftJoin('degree_types', function($join){
            $join->on('degree_types.degree_type_id', '=', 'educations.degree_type');
        })->leftJoin('translations AS areas_of_study_translation', function($join){
            $join->on('areas_of_study_translation.en', '=', 'areas_of_study.name');
        })->leftJoin('translations AS degree_types_translation', function($join){
            $join->on('degree_types_translation.en', '=', 'degree_types.name');
        });
        if($education_id)
            return $queryObj->where('educations.education_id', $education_id)->first();
        return $queryObj->orderBy('educations.created_at', 'DESC')->paginate($per_page);
    }
}