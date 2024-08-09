<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CertificationType extends Model
{
    protected $primaryKey = 'certification_type';
    protected $table = 'certification_types';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lcountry',
        'suggestion_id'
    ];

    public function country()
    {
        return $this->belongsTo(ListCountry::class, 'lcountry_id')->first();
    }

    /**
     * Searches for certifications types by its translation accordingly to sent parameters and Session() user_lang
     * @param String name
     * @param Int limit
     * @return Array
     */
    public function getCertificationTypeByNameAndLanguage($name = '', $limit = 10)
    {
        $userLanguage = Session()->has('user_lang') ? Session()->get('user_lang') : ListLangue::DEFAULT_LANGUAGE;
        $certificationsType = CertificationType::where("translations.$userLanguage", 'like', "%$name%")->leftJoin('translations', function($join){
            $join->on('translations.en', 'certification_types.name');
        })->leftJoin('suggestions', function($join){
            $join->on('suggestions.suggestion_id', 'certification_types.suggestion_id');
        })->limit($limit)->get();
        $personId = Auth::user() ? Auth::user()->person_id : null;
        $userLang = Session()->get('user_lang');
        $filteredCertificationTypes = [];
        foreach($certificationsType as $certificationType){
            if(!$certificationType->suggestion_id){
                $filteredCertificationTypes[] = $certificationType;
            }else if($certificationType->suggestion_id && $certificationType->author_id == $personId && $certificationType->lang == $userLang){
                $filteredCertificationTypes[] = $certificationType;
            }
        }
        return $filteredCertificationTypes;
    }
}
