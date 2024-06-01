<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\ChatMessage;
use App\Models\JobList;

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
            Validator::throwResponse('message can not be sent to company', 500);
        $chatAttachment = null;
        if(request('attachment')){
            $imageHandler = Validator::validateImage(request('attachment'));
            $chatAttachment = base64_encode($imageHandler->generateImageThumbanil());
            $imageHandler->destroyFile();
        }
        $chatMessage = (new ChatMessage())->makeMessage($chatMessageObjects['receiver'], $chatMessageObjects['sender'], request('message'), $chatAttachment, request('job_id'));
        if(!$chatAttachment)
            Validator::throwResponse('message not sent', 500);
        return response()->json($chatMessage);
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
            Validator::throwResponse('not owner of message', 400);
        if(!$object['chat_message']->delete())
            Validator::throwResponse('chat message not removed', 500);
        return response()->json(['message' => 'chat message removed']);
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
        return response()->json($results);
    }
}
