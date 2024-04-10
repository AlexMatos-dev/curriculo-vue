<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListNeighborhood extends Model
{
    protected $table = 'lneighborhoods';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lneighborhood_name',
        'lneigcity_id '
    ];

    public function listCity()
    {
        return $this->belongsTo(ListCity::class, 'lneigcity_id')->first();
    }
}