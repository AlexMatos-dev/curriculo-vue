<?php

namespace App\Http\Middleware;

use App\Helpers\Validator;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Professional;
use App\Models\Profile;
use App\Models\Recruiter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChatMessageHandler extends Controller
{
    /**
     * Validates parameters and gather and set to session Sender and Receiver information.
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $person = auth('api')->user();
        if(!in_array(request('prefix'), ['professional', 'company', 'recruiter']))
            Validator::throwResponse('invalid prefix', 500);
        $profile = $person->getProfile($this->getProfileType(request('prefix')));
        if(!$profile)
            Validator::throwResponse(request('prefix') . ' ' . translate('profile not found'), 500);
        Session()->put('chatSender', $profile);
        Session()->put('chatSenderType', request('prefix'));
        if($this->isReceiverException())
            return $next($request);
        $receiverTypeProfile = $this->getProfileType(request('receiverType'));
        if(!request('receiverId') || !request('receiverType') || !$receiverTypeProfile)
            Validator::throwResponse('receiver ' . request('receiverType') . ' ' . translate('profile not found'), 500);
        $receiverProfile = $this->getProfileObject($receiverTypeProfile, request('receiverId'));
        if(!$receiverProfile)
            Validator::throwResponse(translate('receiver profile not found'), 500);
        if($profile->person_id == $receiverProfile->person_id)
            Validator::throwResponse(translate('you can not send a message to yourself'), 500);
        Session()->put('chatReceiver', $receiverProfile);
        Session()->put('chatReceiverType', $receiverTypeProfile);
        return $next($request);
    }

    /**
     * Gets the profile type constant accordingly to sen name
     * @param String name
     * @return String
     */
    public function getProfileType($name = '')
    {
        switch($name){
            case 'professional':
                $profileType = Profile::PROFESSIONAL;
            break;
            case 'recruiter':
                $profileType = Profile::RECRUITER;
            break;
            case 'company':
                $profileType = Profile::COMPANY;
            break;
            default:
                $profileType = '';
            break;
        }
        return $profileType;
    }

    /**
     * Gets the object accordingly to profile type and id
     * @param String profileType
     * @param Int profileId
     * @return Object (Professional, Recruiter or Company)
     */
    public function getProfileObject($profileType = null, $profileId = null)
    {
        switch($profileType){
            case Profile::PROFESSIONAL:
                $object = Professional::where('persons.person_id', $profileId)->leftJoin('persons', function($join){
                    $join->on('persons.person_id', 'professionals.person_id');
                })->first();
            break;
            case Profile::RECRUITER:
                $object = Recruiter::where('persons.person_id', $profileId)->leftJoin('persons', function($join){
                    $join->on('persons.person_id', 'professionals.person_id');
                })->first();
            break;
            case Profile::COMPANY:
                $object = Company::where('companies_admins.person_id', $profileId)->leftJoin('companies_admins', function($join){
                    $join->on('companies_admins.company_id', 'companies.company_id');
                })->first();
            break;
            default:
                $object = null;
            break;
        }
        return $object;
    }

    /**
     * Checkes if route requires Receiver validation
     * @return Bool
     */
    public function isReceiverException()
    {
        $exceptionRoutes = [
            'api/chat_message/{prefix}/remove',
            'api/chat_message/{prefix}/list'
        ];
        if(!in_array($this->request->route()->uri(), $exceptionRoutes))
            return false;
        return true;
    }
}
