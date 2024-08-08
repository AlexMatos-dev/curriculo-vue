<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Stichoza\GoogleTranslate\GoogleTranslate;

class Tag extends Model
{
    protected $primaryKey = 'tags_id';
    protected $table = 'tags';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tags_name',
        'suggestion_id'
    ];

    /**
     * Searches for tags by its translation accordingly to sent parameters and Session() user_lang
     * @param String name
     * @param Int limit
     * @return Array
     */
    public function getTagByNameAndLanguage($name = '', $limit = 10)
    {
        $userLanguage = Session()->has('user_lang') ? Session()->get('user_lang') : ListLangue::DEFAULT_LANGUAGE;
        $tags = Tag::where("translations.$userLanguage", 'like', "%$name%")->leftJoin('translations', function($join){
            $join->on('translations.en', 'tags.tags_name');
        })->leftJoin('suggestions', function($join){
            $join->on('suggestions.suggestion_id', 'tags.suggestion_id');
        })->limit($limit)->get();
        $personId = Auth::user() ? Auth::user()->person_id : null;
        $userLang = Session()->get('user_lang');
        $filteredTags = [];
        foreach($tags as $tag){
            if(!$tag->suggestion_id){
                $filteredTags[] = $tag;
            }else if($tag->suggestion_id && $tag->author_id == $personId && $tag->lang == $userLang){
                $filteredTags[] = $tag;
            }
        }
        return $filteredTags;
    }

    /**
     * Create a new tag
     * @param String tagName
     * @param String langIso - to set tag name at translations
     * @return Object|Boolean
     */
    public function createTag($tagName = '', $langIso = 'en')
    {
        if(!$tagName || $tagName == '')
            return false;
        if(!in_array($langIso, Translation::OFFICIAL_LANGUAGES))
            $langIso = 'en';
        $result = Tag::create([
            'tags_name' => $tagName
        ]);
        if(!$result)
            return false;
        $pt = $langIso == 'pt' ? $tagName : null;
        $es = $langIso == 'es' ? $tagName : null;
        if(!$pt){
            $googleTranslator = new GoogleTranslate('pt', 'en');
            $pt = $googleTranslator->translate($tagName);
        }
        if(!$es){
            $googleTranslator = new GoogleTranslate('es', 'en');
            $es = $googleTranslator->translate($tagName);
        }
        Translation::create([
            'en' => $tagName,
            'pt' => $pt,
            'es' => $es,
            'category' => Translation::CATEGORY_TAG
        ]);
        return $result;
    }

    /**
     * Tries to return a Tag matching sent parameters
     * @param String tagName
     * @param String lang - default = 'en'
     * @return Object|False
     */
    public function findTagByNameAndLang($tagName, $lang = 'en')
    {
        return Tag::leftJoin('translations', function($join){
            $join->on("translations.en", 'tags.tags_name');
        })->where("translations.$lang", $tagName)->whereOr('translations.en', $tagName)->first();
    }
}