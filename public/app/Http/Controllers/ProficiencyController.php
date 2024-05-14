<?php

namespace App\Http\Controllers;

use App\Models\Proficiency;
use Illuminate\Http\Request;

class ProficiencyController extends Controller
{
    public function index(Request $request)
    {
        $categoryName = $request->query('category', '');

        if ($category = Proficiency::getCategory($categoryName))
        {
            $proficiencies = Proficiency::where('category', $category)->get();
        }
        else
        {
            $proficiencies = Proficiency::all();
        }

        return response()->json($proficiencies, 200);
    }
}
