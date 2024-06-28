<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\Curriculum;
use App\Models\Link;
use Stichoza\GoogleTranslate\GoogleTranslate;

class LinkController extends Controller
{
    /**
     * Get all link of logged professional Curriculum.
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
        $links = (new Link())->getAllMyLinks(request('per_page', 100), $this->getProfessionalBySession()->professional_id, $this->getCurriculumBySession());
        returnResponse($links);
    }

    /**
     * Creates a link.
     * @param String link_id  (in case of update)
     * @param String link_type - required
     * @param Date url - required
     * @param Date curriculum_id - required
     * @return \Illuminate\Http\JsonResponse 
     */
    public function store()
    {
        Validator::validateParameters($this->request, [
            'link_id' => 'numeric',
            'link_type' => 'required|max:100',
            'url' => 'required|max:100',
            'curriculum_id' => 'numeric|required'
        ]);
        if(!in_array(request('link_type'), Link::LINK_TYPES))
            Validator::throwResponse(translate('link type not valid'), 400);
        $link = Link::create($this->request->all());
        if(!$link)
            Validator::throwResponse(translate('link not created'), 500);
        returnResponse($link);
    }

    /**
     * Update the specified link in storage.
     * @param Int link_id - required
     * @param Int link_type - required
     * @param Int url - required
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        $link = (new Link())->isFromProfessionalCurriculum(request('link'), $this->getProfessionalBySession()->professional_id);
        if(!$link)
            Validator::throwResponse(translate('link not found'), 400);
        Validator::validateParameters($this->request, [
            'link_id' => 'numeric|required',
            'link_type' => 'required|max:100',
            'url' => 'required|max:100'
        ]);
        $link->update($this->request->all());
        if(!$link)
            Validator::throwResponse(translate('link not updated'), 500);
        returnResponse($link);
    }

    /**
     * Display the specified Link.
     * @param Int link - required (link id)
     */
    public function show()
    {
        $link = (new Link())->isFromProfessionalCurriculum(request('link'), $this->getProfessionalBySession()->professional_id);
        if(!$link)
            Validator::throwResponse(translate('link not found'), 400);
        returnResponse($link);
    }

    /**
     * Remove the specified link.
     * @param Int link - required (link id)
     * @return \Illuminate\Http\JsonResponse 
     */
    public function destroy()
    {
        $link = (new Link())->isFromProfessionalCurriculum(request('link'), $this->getProfessionalBySession()->professional_id);
        if(!$link)
            Validator::throwResponse(translate('link not found'), 400);
        if(!$link->delete())
            Validator::throwResponse(translate('link not removed'), 500);
        returnResponse(['message' => translate('link removed')]);
    }
}
