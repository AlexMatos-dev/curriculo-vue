<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proficiency extends Model
{
    const CATEGORY_LANGUAGE = 'language';
    const CATEGORY_SENIORITY = 'seniority';
    const CATEGORY_LEVEL = 'level';

    protected $primaryKey = 'proficiency_id';
    protected $table = 'proficiency';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'proficiency_level',
        'category',
        'weight'
    ];

    /**
     * Get category constant by name
     * @param String name
     * @return Stirng|null
     */
    public function getCategory($name = '')
    {
        if(!in_array($name, [$this::CATEGORY_LANGUAGE, $this::CATEGORY_SENIORITY, $this::CATEGORY_LEVEL]))
            return null;
        switch($name){
            case 'language':
                return $this::CATEGORY_LANGUAGE;
            break;
            case 'seniority':
                return $this::CATEGORY_SENIORITY;
            break;
            case 'level':
                return $this::CATEGORY_LEVEL;
            break;
        }
    }

    /**
     * Gets all proficiencies by category name
     * @param String name
     * @param Bool asArray - To return an array
     * @return Array - Of Proficiency objects or parsed as array
     */
    public function getProficiencies($name = '', $asArray = false)
    {
        if(!in_array($name, [$this::CATEGORY_LANGUAGE, $this::CATEGORY_SENIORITY, $this::CATEGORY_LEVEL]))
            return null;
        $data = Proficiency::where('category', $name)->get(); 
        if($asArray){
            $results = [];
            foreach($data as $obj){
                $results[$obj->proficiency_id] = $obj->proficiency_id;
            }
            return $results;
        }
        return $data;
    }
}