<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListLangue extends Model
{
    const DEFAULT_LANGUAGE = 'en';
    
    protected $primaryKey = 'llangue_id';
    protected $table = 'listlangues';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'llangue_name',
        'llangue_acronyn'
    ];

    /**
     * Returns an array of all unofficial languages
     * @return Array
     */
    public function getNotOficialLangsIso()
    {
        $data = [];
        $results = ListLangue::whereNotIn('llangue_acronyn', Translation::OFFICIAL_LANGUAGES)->get();
        foreach($results as $language){
            $data[$language->llangue_acronyn] = $language;
        }
        return $data;
    }
}