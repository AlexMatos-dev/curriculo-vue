<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\Curriculum;
use App\Models\ListLangue;

class CurriculumController extends Controller
{
    /**
     * Get all curriculumns of logged professional.
     * @param Int per_page
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {   
        Validator::validateParameters($this->request, [
            'per_page' => 'numeric',
            'curriculum_type' => "string|in:".Curriculum::TYPE_FILE.','.Curriculum::TYPE_INFO
        ]);
        $parameters = [
            'per_page' => request('per_page', 100),
            'curriculum_type' => request('curriculum_type')
        ];
        $curriculums = (new Curriculum())->getAllMyCurriculums($parameters, $this->getProfessionalBySession()->professional_id);
        return response()->json($curriculums);
    }

    /**
     * Creates or Updates a curriculum.
     * @param String curriculum_id  (in case of update)
     * @param String curriculum_file
     * @param Int clengua_id - required
     * @return \Illuminate\Http\JsonResponse 
     */
    public function store()
    {
        Validator::validateParameters($this->request, [
            'curriculum_id' => 'numeric',
            'curriculum_file' => 'string',
            'clengua_id' => 'numeric|required'
        ]);
        Validator::checkExistanceOnTable([
            'clengua_id' => ['data' => request('clengua_id'), 'object' => ListLangue::class]
        ]);
        $professional = $this->getProfessionalBySession();
        if(!$professional)
            Validator::throwResponse(translate('no professional found'), 400);
        $curriculum = new Curriculum();
        if(request('curriculum_id')){
            $foundResult = Curriculum::find(request('curriculum_id'));
            if(!$foundResult || $foundResult->cprofes_id != $professional->professional_id)
                Validator::throwResponse(translate('curriculum not found'), 400);
            $curriculum = $foundResult;
        }else{
            $curriculum->cprofes_id = $professional->professional_id;
            $curriculum->curriculum_type = request('curriculum_file') ? Curriculum::TYPE_FILE : Curriculum::TYPE_INFO;
        }
        $curriculum->clengua_id = request('clengua_id');
        switch($curriculum->curriculum_type){
            case Curriculum::TYPE_FILE:
                $imageHandler = Validator::validateImage(request('curriculum_file'));
                $curriculum->curriculum_file = base64_encode($imageHandler->generateImageThumbanil());
                $imageHandler->destroyFile();
            break;
        }
        $errorMessage = request('curriculum_id') ? translate('curriculum not updated') : translate('curriculum not created');
        if(!$curriculum->save())
            Validator::throwResponse($errorMessage, 500);
        return response()->json($curriculum);
    }

    /**
     * Display the specified Curriculum.
     * @param Int curriculum - required (curriculum id)
     */
    public function show()
    {
        $curriculum = (new Curriculum())->isFromProfessionalCurriculum(request('curriculum'), $this->getProfessionalBySession()->professional_id);
        if(!$curriculum)
            Validator::throwResponse(translate('curriculum not found'), 400);
        return response()->json($curriculum);
    }

    /**
     * Remove the specified curriculum.
     * @param Int curriculum - required (curriculum id)
     * @return \Illuminate\Http\JsonResponse 
     */
    public function destroy()
    {
        $curriculum = Curriculum::where('curriculum_id', request('curriculum'))->where('cprofes_id', $this->getProfessionalBySession()->professional_id)->first();
        if(!$curriculum)
            Validator::throwResponse(translate('curriculum not found'), 400);
        $error = false;
        try {
            \App\Models\Experience::where('excurriculum_id', $curriculum->curriculum_id)->delete();
            \App\Models\Certification::where('cercurriculum_id', $curriculum->curriculum_id)->delete();
            \App\Models\Education::where('edcurriculum_id', $curriculum->curriculum_id)->delete();
            \App\Models\Skill::where('skcurriculum_id', $curriculum->curriculum_id)->delete();
            \App\Models\Reference::where('refcurriculum_id', $curriculum->curriculum_id)->delete();
            \App\Models\Visa::where('vicurriculum_id', $curriculum->curriculum_id)->delete();
            \App\Models\Country::where('curriculum_id', $curriculum->curriculum_id)->delete();
            \App\Models\Presentation::where('precurriculum_id', $curriculum->curriculum_id)->delete();
            \App\Models\Link::where('curriculum_id', $curriculum->curriculum_id)->delete();
            $curriculum->delete();
        } catch (\Throwable $th) {
            $error = true;
        }
        if($error)
            Validator::throwResponse(translate('curriculum not removed'), 500);
        return response()->json(['message' => translate('curriculum removed')]);
    }
}
