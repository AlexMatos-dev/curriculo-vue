<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;

class Link extends Model
{
    const LINK_TYPES = [
        'youtube',
        'linkedin',
        'facebook',
        'external',
        'instagram'
    ];

    protected $primaryKey = 'link_id';
    protected $table = 'links';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'curriculum_id',
        'link_type',
        'url'
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'curriculum_id')->first();
    }

    /**
     * Get all links from sent professional id, paginated or not
     * @param Integer perPage - if sent will paginate results
     * @param Integer professionlId
     * @param Curriculum curriculum - to get link of this curriculum
     * @return ArrayOfLinks
     */
    public function getAllMyLinks($perPage = null, $professional_id = null, Curriculum|False $curriculum = null)
    {
        $this->joinQueryParam = ['professional_id' => $professional_id, 'curriculum_id' => $curriculum ? $curriculum->curriculum_id : null];
        if($curriculum){
            $queryObj = Link::join('curriculums', function (JoinClause $join) {
                $join->on('links.curriculum_id', '=', 'curriculums.curriculum_id')
                    ->where('curriculums.cprofes_id', '=', $this->joinQueryParam['professional_id'])
                    ->where('links.curriculum_id', '=', $this->joinQueryParam['curriculum_id']);
            });
        }else{
            $queryObj = Link::join('curriculums', function (JoinClause $join) {
                $join->on('links.curriculum_id', '=', 'curriculums.curriculum_id')
                    ->where('curriculums.cprofes_id', '=', $this->joinQueryParam['professional_id']);
            });
        }
        if(!$perPage){
            $links = $queryObj->get();
        }else{
            $links = $queryObj->paginate($perPage);
        }
        return $links;
    }

    /**
     * Checks if link id belongs to one of the professional curriculum
     * @param Integer link_id
     * @param Integer professional_id
     * @return Link|Null
     */
    public function isFromProfessionalCurriculum($link_id = null, $professional_id = null)
    {
        if(!$link_id || !$professional_id)
            return null;
        $this->joinQueryParam = ['professional_id' => $professional_id, 'link_id' => $link_id];
        return Link::join('curriculums', function (JoinClause $join) {
            $join->on('links.curriculum_id', '=', 'curriculums.curriculum_id')
                ->where('curriculums.cprofes_id', '=', $this->joinQueryParam['professional_id'])
                ->where('links.link_id', '=', $this->joinQueryParam['link_id']);
        })->first();
    }
}