<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Professional extends Model
{
    protected $primaryKey = 'professional_id';
    protected $table = 'professionals';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'person_id',
        'professional_slug',
        'professional_firstname',
        'professional_lastname',
        'professional_email',
        'professional_phone',
        'professional_photo',
        'professional_cover',
        'professional_title',
        'currently_working',
        'avaliable_to_travel',
        'paying'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id')->first();
    }

    /**
     * Creates or Updates $this Professional by sent data
     * @param Array - Schema [$attrName => $attrValue]
     * @return Profession|False
     */
    public function saveProfessional($data = [])
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

    /**
     * Gets this Professional DataPerson object, if it exists
     * @return DataPerson|Null
     */
    public function getDataPerson()
    {
        return DataPerson::where('dpprofes_id', $this->professional_id)->first();
    }
}