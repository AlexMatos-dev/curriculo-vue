<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;

class Visa extends Model
{
    protected $primaryKey = 'visas_id';
    protected $table = 'visas';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vicurriculum_id',
        'vicountry_id',
        'visa_type'
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'vicurriculum_id')->first();
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'vicountry_id')->first();
    }

    public function visaType()
    {
        return $this->belongsTo(TypeVisas::class, 'visa_type')->first();
    }

    /**
     * Get all visas from sent professional id, paginated or not
     * @param Integer perPage - if sent will paginate results
     * @param Integer professionlId
     * @return ArrayOfLinks
     */
    public function getAllMyVisas($perPage = null, $professional_id = null, Curriculum|False $curriculum = null)
    {
        if($curriculum){
            $queryObj = Visa::where('vicurriculum_id', $curriculum->curriculum_id)->leftJoin('curriculums AS curriculum', function($join) use ($professional_id){
                $join->on('visas.vicurriculum_id', '=', 'curriculum.curriculum_id')
                ->where('curriculum.cprofes_id', '=', $professional_id);
            })->leftJoin('countries AS country', function($join){
                $join->on('visas.vicountry_id', '=', 'country.country_id');
            });
        }else{
            $queryObj = Visa::leftJoin('curriculums AS curriculum', function($join) use ($professional_id){
                $join->on('visas.vicurriculum_id', '=', 'curriculum.curriculum_id')
                ->where('curriculum.cprofes_id', '=', $professional_id);
            })->leftJoin('countries AS country', function($join){
                $join->on('visas.vicountry_id', '=', 'country.country_id');
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
     * Checks if visa id belongs to one of the professional curriculum
     * @param Integer visa_id
     * @param Integer professional_id
     * @return Visa|Null
     */
    public function isFromProfessionalCurriculum($visa_id = null, $professional_id = null)
    {
        if(!$visa_id || !$professional_id)
            return null;
        $this->joinQueryParam = ['professional_id' => $professional_id];
        return Visa::where('visas_id', $visa_id)->leftJoin('curriculums AS curriculum', function($join){
            $join->on('visas.vicurriculum_id', '=', 'curriculum.curriculum_id')
            ->where('curriculum.cprofes_id', '=', $this->joinQueryParam['professional_id']);
        })->leftJoin('countries AS country', function($join){
            $join->on('visas.vicountry_id', '=', 'country.country_id');
        })->first();
    }
}