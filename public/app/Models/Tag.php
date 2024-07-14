<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'tags_name'
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
        return Tag::where("translations.$userLanguage", 'like', "%$name%")->leftJoin('translations', function($join){
            $join->on('translations.en', 'tags.tags_name');
        })->limit($limit)->get();
    }
}