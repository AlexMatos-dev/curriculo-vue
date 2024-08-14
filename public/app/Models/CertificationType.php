<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Stichoza\GoogleTranslate\GoogleTranslate;

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
    
    /**
     * Tries to return a CertificationType matching sent parameters
     * @param String certificationName
     * @param Int countryId - optional
     * @param String lang - default = 'en'
     * @return Object|False
     */
    public function findCertificationByNameAndLang($certificationName, $countryId = null, $lang = 'en')
    {
        $query = CertificationType::leftJoin('translations', function($join){
            $join->on("translations.en", 'certification_types.certification_type');
        })->where("translations.$lang", $certificationName)->whereOr('translations.en', $certificationName);
        if($countryId){
            $query->where('certification_types.lcountry', $countryId);
        }
        return $query->first();
    }

    /**
     * Create a new certification 
     * @param String certificationName
     * @param Int countryId - optional
     * @param String langIso - to set tag name at translations
     * @return Object|Boolean
     */
    public function createCertification($certificationName = '', $countryId = '', $langIso = 'en')
    {
        if(!$certificationName || $certificationName == '')
            return false;
        if($countryId && (!is_numeric($countryId) || (int)$countryId < 1))
            return false;
        if(!in_array($langIso, Translation::OFFICIAL_LANGUAGES))
            $langIso = 'en';
        $result = CertificationType::create([
            'name' => $certificationName,
            'lcountry' => $countryId,
        ]);
        if(!$result)
            return false;
        $pt = $langIso == 'pt' ? $certificationName : null;
        $es = $langIso == 'es' ? $certificationName : null;
        if(!$pt){
            $googleTranslator = new GoogleTranslate('pt', 'en');
            $pt = $googleTranslator->translate($certificationName);
        }
        if(!$es){
            $googleTranslator = new GoogleTranslate('es', 'en');
            $es = $googleTranslator->translate($certificationName);
        }
        Translation::create([
            'en' => $certificationName,
            'pt' => $pt,
            'es' => $es,
            'category' => Translation::CATEGORY_CERTIFICATION_TYPE
        ]);
        return $result;
    }
}
