<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListLangue extends Model
{
    protected $primaryKey = 'llangue_id';
    protected $table = 'listlangues';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'llangue_name'
    ];

    /**
     * Get the language ISO code from file
     * @return String
     */
    public function getIsoCode()
    {
        $path = storage_path('app/dbSourceFiles/languages.json');
        if(!file_exists($path))
            return '';
        $data = json_decode(file_get_contents($path), true);
        foreach($data as $langue){
            if($langue['ptLang'] == $this->llangue_name)
                return $langue['code'];
        }
        return '';
    }
}