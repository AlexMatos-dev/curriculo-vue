<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $table = 'links';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'curriculum_id',
        'link_type',
        'url'
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'curriculum_id')->first();
    }
}