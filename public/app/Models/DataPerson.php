<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataPerson extends Model 
{
    protected $primaryKey = 'dpperson_id';
    protected $table = 'dataperson';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dpprofes_id',
        'dpdate_of_birth',
        'dpgender',
        'dpcity_id',
        'dpstate_id',
        'dppostal_code',
        'dpcountry_id'
    ];

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'dpgender')->first();
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class, 'dpprofes_id')->first();
    }

    public function listCity()
    {
        return $this->belongsTo(ListCity::class, 'dpcity_id')->first();
    }

    public function listState()
    {
        return $this->belongsTo(ListState::class, 'dpstate_id')->first();
    }

    public function listCounty()
    {
        return $this->belongsTo(ListCountry::class, 'dpcountry_id')->first();
    }

    /**
     * Creates or Updates $this DataPerson by sent data
     * @param Array - Schema [$attrName => $attrValue]
     * @return Profession|False
     */
    public function saveDataPerson($data = [])
    {
        $myAttributes = $this->fillable;
        foreach($data as $attr => $value){
            if(in_array($attr, $myAttributes))
                $this->{$attr} = $value;
        }
        if(!$this->save())
            return false;
        return $this;
    }
}