<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\Proficiency;
use App\Models\Skill;
use App\Models\Tag;

class SkillController extends Controller
{
    /**
     * Get all visas of logged professional Curriculum.
     * @param Int per_page
     * @param Int curriculum_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {   
        Validator::validateParameters($this->request, [
            'per_page' => 'numeric',
            'curriculum_id' => 'numeric'
        ]);
        $skills = (new Skill())->getAllMySkills(request('per_page', 15), $this->getProfessionalBySession()->professional_id, $this->getCurriculumBySession()->curriculum_id);
        return response()->json($skills);
    }

    /**
     * Creates or Updates a skill.
     * @param String visa_id  (in case of update)
     * @param String link_type - required
     * @param Date url - required
     * @return \Illuminate\Http\JsonResponse 
     */
    public function store()
    {
        Validator::validateParameters($this->request, [
            'skcurriculum_id' => 'numeric|required',
            'skill_name' => 'numeric|required',
            'skproficiency_level' => 'numeric|required',
            'experience_level' => 'required|numeric|between:0,100'
        ]);
        Validator::checkExistanceOnTable([
            'skill_name' => ['data' => request('skill_name'), 'object' => Tag::class],
            'skproficiency_level' => ['data' => request('skproficiency_level'), 'object' => Proficiency::class]
        ]);
        $skill = Skill::create($this->request->all());
        if(!$skill)
            Validator::throwResponse('skill not created', 500);
        return response()->json($skill);
    }

    /**
     * Update the specified Education in storage.
     * @param Int skill_name - required
     * @param Int skproficiency_level - required
     * @param Float experience_level - required
     */
    public function update(Skill $skill)
    {
        Validator::validateParameters($this->request, [
            'skill_name' => 'numeric|required',
            'skproficiency_level' => 'numeric|required',
            'experience_level' => 'required|numeric|between:0,100'
        ]);
        Validator::checkExistanceOnTable([
            'skill_name' => ['data' => request('skill_name'), 'object' => Tag::class],
            'skproficiency_level' => ['data' => request('skproficiency_level'), 'object' => Proficiency::class]
        ]);
        $skill->update($this->request->all());
        return response()->json($skill);
    }

    /**
     * Display the specified skill.
     * @param Int skill - required (skill id)
     */
    public function show()
    {
        $skillObj = Skill::find(request('skill'));
        if(!$skillObj)
            Validator::throwResponse('skill not found', 400);
        $skill = $skillObj->isFromProfessionalCurriculum($this->getProfessionalBySession()->professional_id);
        if(!$skill)
            Validator::throwResponse('skill not found', 400);
        return response()->json($skill);
    }

    /**
     * Remove the specified visa.
     * @param Int visa - required (visa id)
     * @return \Illuminate\Http\JsonResponse 
     */
    public function destroy(Skill $skillObj)
    {
        $skill = $skillObj->isFromProfessionalCurriculum($this->getProfessionalBySession()->professional_id);
        if(!$skill)
            Validator::throwResponse('skill not found', 400);
        return response()->json(['message' => 'skill removed']);
    }
}
