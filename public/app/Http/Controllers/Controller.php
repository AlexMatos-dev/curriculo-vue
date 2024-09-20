<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Controller
{
    public $request;
    public function __construct(Request $requestObj)
    {
        $this->request = $requestObj;
    }

    public function getCompanyBySession()
    {
        $companyObj = Session()->get('company');
        if(!$companyObj)
            return false;
        return $companyObj;
    }

    public function getPersonBySession()
    {
        $personObj = Session()->get('person');
        if(!$personObj)
            return false;
        return $personObj;
    }

    public function getProfessionalBySession()
    {
        $professionalObj = Session()->get('professional');
        if(!$professionalObj)
            return false;
        return $professionalObj;
    }

    public function getRecruiterBySession()
    {
        $recruiterObj = Session()->get('recruiter');
        if(!$recruiterObj)
            return false;
        return $recruiterObj;
    }

    public function getCurriculumBySession()
    {
        $curriculumObj = Session()->get('curriculum');
        if(!$curriculumObj)
            return false;
        return $curriculumObj;
    }

    public function getJobBySession()
    {
        $jobListObj = Session()->get('job');
        if(!$jobListObj)
            return false;
        return $jobListObj;
    }

    public function getObjectFromSession()
    {
        $object = Session()->get($this->getObjectType());
        if(!$object)
            return false;
        return $object;
    }

    public function getObjectType()
    {
        return Session()->get('objectType');
    }

    public function getChatMessageObjects()
    {
        return [
            'sender' => Session()->get('chatSender'),
            'senderType' => Session()->get('chatSenderType'),
            'receiver' => Session()->get('chatReceiver'),
            'receiverType' => Session()->get('chatReceiverType')
        ];
    }
}
