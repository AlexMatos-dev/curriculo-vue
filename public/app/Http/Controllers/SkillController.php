<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\Proficiency;
use App\Models\Skill;
use App\Models\Tag;

class SkillController extends Controller
{
    /**
     * Get all skills of logged professional Curriculum.
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
        $skills = (new Skill())->getAllMySkills(request('per_page', 100), $this->getProfessionalBySession()->professional_id, $this->getCurriculumBySession()->curriculum_id);
        returnResponse($skills);
    }

    /**
     * Creates a skill.
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
            'skproficiency_level' => 'numeric|required'
        ]);
        $objects = Validator::checkExistanceOnTable([
            'skill_name' => ['data' => request('skill_name'), 'object' => Tag::class],
            'skproficiency_level' => ['data' => request('skproficiency_level'), 'object' => Proficiency::class]
        ]);
        if($objects['skproficiency_level']->category != Proficiency::CATEGORY_LEVEL)
            Validator::throwResponse(translate('invalid proficiency type'));
        $skill = Skill::create($this->request->all());
        if(!$skill)
            Validator::throwResponse(translate('skill not created'), 500);
        returnResponse($skill);
    }

    /**
     * Update the specified Skill.
     * @param Int skill_name - required
     * @param Int skproficiency_level - required
     * @param Float experience_level - required
     */
    public function update()
    {
        $skillObj = Skill::find(request('skill'));
        if(!$skillObj)
            Validator::throwResponse('skill not found', 400);
        $skill = $skillObj->isFromProfessionalCurriculum($this->getProfessionalBySession()->professional_id);
        if(!$skill)
            Validator::throwResponse('skill not found', 400);
        Validator::validateParameters($this->request, [
            'skcurriculum_id' => 'numeric|required',
            'skill_name' => 'numeric|required',
            'skproficiency_level' => 'numeric|required'
        ]);
        $objects = Validator::checkExistanceOnTable([
            'skill_name' => ['data' => request('skill_name'), 'object' => Tag::class],
            'skproficiency_level' => ['data' => request('skproficiency_level'), 'object' => Proficiency::class]
        ]);
        if($objects['skproficiency_level']->category != Proficiency::CATEGORY_LEVEL)
            Validator::throwResponse('invalid proficiency, must be level type');
        $skill->update($this->request->all());
        returnResponse($skill);
    }

    /**
     * Display the specified skill.
     * @param Int skill - required (skill id)
     */
    public function show()
    {
        $skillObj = Skill::find(request('skill'));
        if(!$skillObj)
            Validator::throwResponse(translate('skill not found'), 400);
        $skill = $skillObj->isFromProfessionalCurriculum($this->getProfessionalBySession()->professional_id);
        if(!$skill)
            Validator::throwResponse(translate('skill not found'), 400);
        returnResponse($skill);
    }

    /**
     * Remove the specified skill.
     * @param Int skill - required (skill id)
     * @return \Illuminate\Http\JsonResponse 
     */
    public function destroy()
    {
        $skillObj = Skill::find(request('skill'));
        if(!$skillObj)
            Validator::throwResponse(translate('skill not found'), 400);
        $skill = $skillObj->isFromProfessionalCurriculum($this->getProfessionalBySession()->professional_id);
        if(!$skill)
            Validator::throwResponse(translate('skill not found'), 400);
        if(!$skill->delete())
            Validator::throwResponse(translate('skill not removed'), 500);
        returnResponse(['message' => translate('skill removed')]);
    }
}
