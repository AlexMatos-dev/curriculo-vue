<?php

namespace App\Http\Controllers;

use App\Helpers\ModelUtils;
use App\Helpers\Validator;
use App\Models\ChatMessage;
use App\Models\JobList;
use Illuminate\Support\Facades\Auth;

class ChatMessageController extends Controller
{
    /**
     * Creates a ChatMessage object
     * @param Int receiverId - required
     * @param String receiverType - required (professional, recruiter or company)
     * @param String message - required
     * @param Stirng attachment - in base 64 string or null
     * @param Int job_id
     */
    public function sendMessage()
    {
        Validator::validateParameters($this->request, [
            'message' => 'required|max:700'
        ]);
        Validator::checkExistanceOnTable([
            'job_id' => ['object' => JobList::class, 'data' => request('job_id'), 'required' => false]
        ]);
        $chatMessageObjects = $this->getChatMessageObjects();
        if($chatMessageObjects['receiverType'] == 'company' && !$chatMessageObjects['receiverObject']->checkPrivacy(ChatMessage::CATEGORY_MESSAGE))
            Validator::throwResponse(translate('message can not be sent to company'), 500);
        $chatAttachment = null;
        if(request('attachment')){
            $imageHandler = Validator::validateImage(request('attachment'));
            $chatAttachment = base64_encode($imageHandler->generateImageThumbanil());
            $imageHandler->destroyFile();
        }
        $chatMessage = (new ChatMessage())->makeMessage($chatMessageObjects['receiver'], $chatMessageObjects['sender'], request('message'), $chatAttachment, request('job_id'));
        if(!$chatAttachment)
            Validator::throwResponse(translate('message not sent'), 500);
        returnResponse($chatMessage);
    }

    /**
     * Removes a message sent by this logged profile
     * @param Int chat_message_id - required
     */
    public function removeMessage()
    {
        Validator::validateParameters($this->request, [
            'chat_message_id' => 'required'
        ]);
        $object = Validator::checkExistanceOnTable([
            'chat_message' => ['object' => ChatMessage::class, 'data' => request('chat_message_id')]
        ]);
        $senderObj = Session()->get('chatSender');
        if($object['chat_message']->sender_message_id != $senderObj->{$senderObj->getKeyName()})
            Validator::throwResponse(translate('not owner of message'), 400);
        if(!$object['chat_message']->delete())
            Validator::throwResponse(translate('chat message not removed'), 500);
        returnResponse(['message' => translate('chat message removed')]);
    }

    /**
     * List all messages related to my profiles
     * @param Int job_id
     * @param Int parsed_by_job
     * @return \Illuminate\Http\JsonResponse - Schema [
     *    "data": Array,
     *    "curent_page": int,
     *    "per_page": int,
     *    "last_page": int,
     * ]
     */
    public function listMessages()
    {
        Validator::validateParameters($this->request, [
            'job_id' => 'integer',
            'parsed_by_job' => 'integer'
        ]);
        Validator::checkExistanceOnTable([
            'job' => ['object' => JobList::class, 'data' => request('job_id'), 'required' => false]
        ]);
        $chatMessageObjects = $this->getChatMessageObjects();
        $chatMessage = new ChatMessage();
        if(request('parsed_by_job')){
            $results = $chatMessage->listChatMessagesByJob($chatMessageObjects);
        }else{
            $results = $chatMessage->listChatMessages($chatMessageObjects, request('job_id'));
        }
        returnResponse($results);
    }

    /**
     * List all notifications of logged user sent profile type
     * @param Integer message_type - one of the constants
     * @param Integer message_read - 1 to true and 0 to false
     * @param String fromData
     * @param String toDate
     * @return \Illuminate\Http\JsonResponse
     */
    public function listNotifications()
    {
        Validator::validateParameters($this->request, [
            'message_type' => 'integer',
            'message_read' => 'integer',
            'fromDate' => 'max:10',
            'toDate' => 'max:10'
        ]);
        $chatMessage = new ChatMessage();
        $profileTypes = $chatMessage->getProfileTypes();
        if(!array_key_exists(request('profile_type'), $profileTypes))
            Validator::throwResponse(translate('invalid profile type'), 400);
        if(request('message_type') && !array_key_exists(request('message_type'), $chatMessage->getMessageType()))
            Validator::throwResponse(translate('invalid message type'), 400);
        $person = Auth::user();
        $profileObj = $person->getProfile($profileTypes[request('profile_type')]);
        if(!$profileObj)
            Validator::throwResponse(translate('profile not found'), 400);

        $profileName = $profileTypes[request('profile_type')];
        $profileId   = $profileObj->{$profileObj->getKeyName()};
        $data = $chatMessage->listNotifications($profileName, $profileId, $this->request);
        $filteredData = [];
        foreach($data as $chatMessage){
            $object = $chatMessage;
            $object->created_at_localized  = ModelUtils::parseDateByLanguage($object->created_at, true);
            $object->updated_at_localized = ModelUtils::parseDateByLanguage($object->updated_at, true);
            $filteredData[] = $object;
        }
        returnResponse([
            'data' => $filteredData
        ]);
    }
}
