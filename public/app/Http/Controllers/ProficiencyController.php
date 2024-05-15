<?php

namespace App\Http\Controllers;

use App\Models\Proficiency;
use Illuminate\Http\Request;

class ProficiencyController extends Controller
{
    public function index(Request $request)
    {
        $categoryName = $request->query('category', '');
        $proficiencies = Proficiency::leftJoin('translations', function($join){
            $join->on('proficiency.proficiency_level', '=', 'translations.en');
        });
        if($category = Proficiency::getCategory($categoryName)){
            $proficiencies->where('category', $category);
        }
        return response()->json($proficiencies->get(), 200);
    }
}
