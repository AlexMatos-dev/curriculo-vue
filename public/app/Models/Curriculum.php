<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;

class Curriculum extends Model 
{
    const TYPE_FILE = 'file';
    const TYPE_INFO = 'info';

    protected $primaryKey = 'curriculum_id';
    protected $table = 'curriculums';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cprofes_id',
        'clengua_id',
        'curriculum_type',
        'curriculum_file'
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class, 'cprofes_id')->first();
    }

    public function listLangue()
    {
        return $this->belongsTo(ListLangue::class, 'clengua_id')->first();
    }

    /**
     * Get all curriculums from sent professional id, paginated or not
     * @param Integer perPage - if sent will paginate results
     * @param Integer professionlId
     * @return ArrayOfLinks
     */
    public function getAllMyCurriculums($perPage = null, $professional_id = null)
    {
        $queryObj = Curriculum::where('cprofes_id', $professional_id);
        if(!$perPage){
            $links = $queryObj->get();
        }else{
            $links = $queryObj->paginate($perPage);
        }
        return $links;
    }

    /**
     * Checks if curriculums id belongs to one of the professional curriculum
     * @param Integer curriculum_id
     * @param Integer professional_id
     * @return Link|Null
     */
    public function isFromProfessionalCurriculum($curriculum_id = null, $professional_id = null)
    {
        if(!$curriculum_id || !$professional_id)
            return null;
        return Curriculum::where('cprofes_id', $professional_id)->where('curriculum_id', $curriculum_id)->first();
    }
}