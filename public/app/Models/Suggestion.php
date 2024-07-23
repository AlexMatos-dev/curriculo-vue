<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    const TAG_SUGGESTION        = 'tags';
    const PROFESSION_SUGGESTION = 'listprofessions';

    protected $primaryKey = 'suggestion_id';
    protected $table = 'suggestions';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'type_id',
        'author_id',
        'lang',
        'suggestion_name'
    ];

    /**
     * Returns all suggestions types avaliable
     * @return Array
     */
    public function suggestionTypes()
    {
        return [
            $this::TAG_SUGGESTION,
            $this::PROFESSION_SUGGESTION
        ];
    }

    /**
     * Saves a new Suggestion
     * @param String type
     * @param Int typeId
     * @param String suggestionName
     * @return Bool
     */
    public function saveSuggestion($type = '', $typeId = null, $suggestionName)
    {
        if(!in_array($type, $this->suggestionTypes()))
            return false;
        $this->type = $type;
        $this->type_id = $typeId;
        $this->suggestion_name = $suggestionName;
        return $this->save();
    }
}
