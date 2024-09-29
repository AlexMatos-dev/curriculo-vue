<?php

namespace App\Models;

use App\Helpers\ModelUtils;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recruiter extends Model
{
    use SoftDeletes;
    
    protected $primaryKey = 'recruiter_id';
    protected $table = 'recruiters';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'person_id',
        'recruiter_photo',
        'paying'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->first();
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id')->first();
    }

    /**
     * Free search for recruiters accordingly to sent parameters
     * @param String key - either 'name', 'email' or 'all'
     * @param String term
     * @param Array notInId - recruiter id to not return
     * @return ArrayOfRecruiter
     */
    public function search($key = 'name', $term = '', $notInId = [])
    {
        $vals = [
            'name'  => 'persons.person_username',
            'email' => 'persons.person_email',
            'all'   => 'all'
        ];
        $results = [];
        if(!array_key_exists($key, $vals))
            return $results;
        $queryObj = Recruiter::select(
            'recruiters.recruiter_id', 'recruiters.company_id', 'recruiters.recruiter_photo', 'persons.*'
        )->whereNotIn('recruiters.recruiter_id', $notInId)->leftJoin('persons', function($join){
            $join->on('recruiters.person_id', '=', 'persons.person_id');
        })->orderBy('paying', 'DESC')->limit(10);
        if($key == 'all'){
            $queryObj->where(function($query) use ($vals, $term) {
                foreach($vals as $val){
                    if($val == 'all')
                        continue;
                    $query->orWhere($val, 'LIKE', '%'.$term.'%');
                }
            });
        }else{
            $queryObj->where($vals[$key], 'LIKE', '%'.$term.'%');
        }
        foreach($queryObj->get() as $recruiter){
            $obj = $recruiter;
            $obj->recruiter_photo = $obj->recruiter_photo ? $obj->recruiter_photo : null;
            $obj->createdAt = ModelUtils::parseDateByLanguage($obj->created_at);
            $obj->updatedAt = ModelUtils::parseDateByLanguage($obj->updated_at);
            $results[] = $obj;
        }
        return $results;
    }
}