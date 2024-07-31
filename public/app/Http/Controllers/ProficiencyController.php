<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\Proficiency;
use Illuminate\Http\Request;

class ProficiencyController extends Controller
{
    /**
     * List all proficiency
     * @param String category
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Validator::validateParameters($request, [
            'category' => 'in:'.implode(',', [Proficiency::CATEGORY_LANGUAGE, Proficiency::CATEGORY_LEVEL, Proficiency::CATEGORY_SENIORITY])
        ]);
        $categoryName = $request->query('category', '');
        $proficiencies = Proficiency::leftJoin('translations', function($join){
            $join->on('proficiency.proficiency_level', '=', 'translations.en');
        });
        if($category = Proficiency::getCategory($categoryName)){
            $proficiencies->where('proficiency.category', $category);
        }
        returnResponse($proficiencies->get(), 200);
    }
}
