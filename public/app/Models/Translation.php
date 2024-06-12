<?php

namespace App\Models;

use App\Helpers\ModelUtils;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    const OFFICIAL_LANGUAGES = ['en', 'pt', 'es'];

    const CATEGORY_SYSTEM_TRANSLATIONS         = 'system_translation';
    const CATEGORY_AREA_OF_STUDIES             = 'area_of_studies_translation';
    const CATEGORY_COMMON_CURRENCIES           = 'common_currencies_translation';
    const CATEGORY_COMPANY_SOCIAL_NETWORK_TYPE = 'company_social_network_type_translation';
    const CATEGORY_COMPANY_TYPE                = 'company_type_translation';
    const CATEGORY_COUNTRY                     = 'country_translation';
    const CATEGORY_DEGREE_TYPE                 = 'degree_type_translation';
    const CATEGORY_GENDER                      = 'gender_translation';
    const CATEGORY_JOB_MODALITY                = 'job_modality_translation';
    const CATEGORY_LANGUAGE                    = 'language_translation';
    const CATEGORY_PROFESSION                  = 'profession_translation';
    const CATEGORY_PROFICIENCY                 = 'proficiency_translation';
    const CATEGORY_TAG                         = 'tag_translation';
    const CATEGORY_VISA_TYPE                   = 'visa_type_translation';
    const CATEGORY_PROFESSION_TYPE             = 'category_type_translation';

    protected $primaryKey = 'translation_id';
    protected $table = 'translations';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'en',
        'pt',
        'es',
        'unofficial_translations',
        'category'
    ];

    /**
     * Tries to return the request translation for sent text at sent language iso
     * Obs: Language iso must no be one of the official languages
     * @param String languageIso
     * @param String text - if NULL gets the EN values
     * @return String|EmptyString found translation
     */
    public function findInUnofficialTranslations($languageIso = '', $text = null)
    {
        if(in_array($languageIso, $this::OFFICIAL_LANGUAGES))
            return '';
        $translationsArray = $this->unofficial_translations ? json_decode($this->unofficial_translations, true) : [];
        if(!array_key_exists($languageIso, $translationsArray))
            return '';
        return $translationsArray[$languageIso];
    }

    /**
     * Add new translation to unofficial_translations attribute by sent data
     * @param String translation
     * @param String languageIso
     * @return Bool
     */
    public function addTranslationsToUnofficialTranslations($translation = '', $languageIso = '')
    {
        if(!$languageIso || !$translation || in_array($languageIso, $this::OFFICIAL_LANGUAGES))
            return false;
        $translationsArray = $this->unofficial_translations ? json_decode($this->unofficial_translations, true) : [];
        $translationsArray[$languageIso] = $translation;
        $this->unofficial_translations = json_encode($translationsArray);
        return $this->save();
    }

    /**
     * Returns $this translation accordingly to request $languageIso, perform the update of the 'unofficial_translations' if needed
     * @param String languageIso
     * @return String
     */
    public function getTranslationByIsoCode($languageIso = 'en')
    {
        if(in_array($languageIso, $this::OFFICIAL_LANGUAGES))
            return $this->{$languageIso} ? $this->{$languageIso} : $this->en;
        $unofficialTranslation = $this->findInUnofficialTranslations($languageIso);
        if(!$unofficialTranslation)
            $this->translateUnofficialTranslation($languageIso);
        $foundValue = $this->findInUnofficialTranslations($languageIso);
        return $foundValue ? $foundValue : $this->en;
    }

    /**
     * Translate and set new translation to $this obj unofficial_translations attribute
     * @param String languageIso
     * @return Bool
     */
    public function translateUnofficialTranslation($languageIso = 'en')
    {
        if(in_array($languageIso, $this::OFFICIAL_LANGUAGES))
            return false;
        $googleTranslator = new \Stichoza\GoogleTranslate\GoogleTranslate($languageIso, 'en');
        $translatedText = $googleTranslator->translate($this->en);
        if(!$translatedText)
            return false;
        return $this->addTranslationsToUnofficialTranslations($translatedText, $languageIso);
    }

    public function getTranslations(Array $textToTranslate, String $languageISO = 'en')
    {
        $translationsObjects = Translation::whereIn('en', $textToTranslate)->get();
        $translatedText = [];
        foreach($translationsObjects as $translation){
            $translatedText[$translation->en] = $translation->getTranslationByIsoCode($languageISO);
        }
        return $translatedText;
    }

    public function getTranslationsByCategory($category, $languageISO = 'en')
    {
        if(!$category)
            return [];
        $translations = Translation::where('category', $category)->get();
        $results = [];
        $officialLanguage = in_array($languageISO, $this::OFFICIAL_LANGUAGES) ? true : false;
        foreach($translations as $translation){
            if(!$officialLanguage){
                $translation->getTranslationByIsoCode($languageISO);
                $text = $translation->getTranslationByIsoCode($languageISO);
            }else{
                $text = $translation->{$languageISO};
            }
            $results[$translation->en] = $text;
        }
        return $results;
    }

    /**
     * Gets all translations indexed with key as EN property
     * @param String category
     * @param Bool addAllLanguagesIso - to set all languages iso on response
     * @return Array - Schema: [$enTranslation => [$languageISO => $translation]]
     */
    public function getAllTranslationsAsEnForIndex($category = null, $addAllLanguagesIso = false)
    {
        $unofficialLanguaguesIso = ListLangue::whereNotIn('llangue_acronyn', Translation::OFFICIAL_LANGUAGES)->get();
        $translations = $category ? Translation::where('category', $category)->get() : Translation::all();
        $translationsArray = [];
        foreach($translations as $translation){
            $thisTranslations = [];
            foreach(Translation::OFFICIAL_LANGUAGES as $officialLangIso){
                $thisTranslations[$officialLangIso] = $translation->$officialLangIso;
            }
            if($addAllLanguagesIso){
                $unofficalArray = $translation->unofficial_translations ? json_decode($translation->unofficial_translations, true) : [];
                foreach($unofficialLanguaguesIso as $unofficalIso){
                    $iso = $unofficalIso->llangue_acronyn;
                    $thisTranslations[$iso] = array_key_exists($iso, $unofficalArray) ? $unofficalArray[$iso] : null;
                }
            }
            $translationsArray[$translation->en] = $thisTranslations;
        }
        return $translationsArray;
    }

    /**
     * Saves a new translation based on sent EN text
     * @param String en - required
     * @param String category - required
     * @return Bool
     */
    public function fabricateTranslation($en, $category)
    {
        if(!in_array($category, ModelUtils::getClassConstants($this)))
            $category = $this::CATEGORY_SYSTEM_TRANSLATIONS;
        $transObj = new Translation();
        $transObj->category = $category;
        $transObj->en = $en;
        foreach($this::OFFICIAL_LANGUAGES as $langIso){
            $googleTranslatorObj = new \Stichoza\GoogleTranslate\GoogleTranslate($langIso, 'en');
            $transObj->{$langIso} = $googleTranslatorObj->translate($en);
        }
        return $transObj->save();
    }
}
