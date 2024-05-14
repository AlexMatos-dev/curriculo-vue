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
     * @param string $name
     * @return string|null
     */
    public static function getCategory($name = '')
    {
        $categories = [
            'language' => self::CATEGORY_LANGUAGE,
            'seniority' => self::CATEGORY_SENIORITY,
            'level' => self::CATEGORY_LEVEL,
        ];

        return $categories[$name] ?? null;
    }
}
