<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;

class Skill extends Model
{
    protected $primaryKey = 'skill_id';
    protected $table = 'skills';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'skcurriculum_id',
        'skill_name',
        'skproficiency_level'
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'skcurriculum_id')->first();
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'skill_name')->first();
    }

    public function profeciency()
    {
        return $this->belongsTo(Proficiency::class, 'skproficiency_level')->first();
    }

    /**
     * Get all skills from sent professional id, paginated or not
     * @param Integer perPage - if sent will paginate results
     * @param Integer professionlId
     * @return ArrayOfLinks
     */
    public function getAllMySkills($perPage = null, $professional_id = null, $curriculum_id = null)
    {
        $queryObj = Skill::leftJoin('curriculums AS curriculum', function($join) use ($professional_id, $curriculum_id){
            $join->on('skills.skcurriculum_id', '=', 'curriculum.curriculum_id')
            ->where('curriculum.cprofes_id', '=', $professional_id)
            ->where('curriculum.curriculum_id', '=', $curriculum_id);
        });
        if(!$perPage){
            $links = $queryObj->get();
        }else{
            $links = $queryObj->paginate($perPage);
        }
        return $links;
    }

    /**
     * Checks if skill id belongs to one of the professional curriculum
     * @param Integer professional_id
     * @return Skill|Null
     */
    public function isFromProfessionalCurriculum($professional_id = null)
    {
        return Skill::join('curriculums', function($join) use ($professional_id){
            $join->on('skills.skcurriculum_id', '=', 'curriculums.curriculum_id')
                ->where('curriculums.cprofes_id', '=', $professional_id);
        })->first();
    }
}