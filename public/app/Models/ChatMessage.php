<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use SoftDeletes;

    const CATEGORY_NOTIFICATION = 'notification';
    const CATEGORY_MESSAGE      = 'message';

    const MESSAGE_SEPARATOR     = '!@!@!';

    const REFERENCES = [
        'professional' => Professional::class,
        'company'      => Company::class,
        'recruiter'    => Recruiter::class,
        'person'       => Person::class
    ];

    const TYPE_JOB_STATUS_CHANGED = 1;
    const TYPE_JOB_APPLIED        = 2;

    protected $primaryKey = 'chat_message_id';
    protected $table = 'chat_messages';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'message',
        'sender_message_id',
        'sender_message_table_name',
        'receiver_message_id',
        'receiver_message_table_name',
        'chat_attachment_id ',
        'message_read',
        'category',
        'job_id'
    ];

    public function attachment()
    {
        return $this->belongsTo(ChatAttachment::class, 'chat_attachment_id');
    }

    /**
     * Generates a notification between sent parameters
     * @param Object receiverObject - required
     * @param Int messageType - required
     * @param String changedData
     * @param Object senderObject
     * @param Int job_id
     * @return ChatMessage|False
     */
    public function makeNotification(Object $receiverObject, Int $messageType = null, $changedData = '', Object $senderObject = null, $job_id = null)
    {
        if(!$messageType || !$this->getMessageType($messageType))
            return false;
        if(!in_array(get_class($receiverObject), $this::REFERENCES))
            return false;
        $senderObject = $senderObject ? $senderObject : $this->getNotifier();
        if(!in_array(get_class($senderObject), $this::REFERENCES))
            return false;
        $message = $this->getMessageType($messageType);
        if(!$message)
            return false;
        if($changedData)
            $message .= $this::MESSAGE_SEPARATOR . $changedData;
        return ChatMessage::create([
            'message' => $message,
            'sender_message_id' => $senderObject->{$senderObject->getKeyName()},
            'sender_message_table_name' => $senderObject->getTable(),
            'receiver_message_id' => $receiverObject->{$receiverObject->getKeyName()},
            'receiver_message_table_name' => $receiverObject->getTable(),
            'category' => $this::CATEGORY_NOTIFICATION,
            'job_id' => $job_id
        ]);
    }

    /**
     * Generates a notification between sent parameters
     * @param Object receiverObject - required
     * @param Object senderObject - required
     * @param String message - required
     * @param String imageSource
     * @param Int job_id
     * @return ChatMessage|False
     */
    public function makeMessage(Object $receiverObject, Object $senderObject = null, String $message, String $imageSource = null, Int $job_id = null)
    {
        if(!in_array(get_class($receiverObject), $this::REFERENCES) || !in_array(get_class($senderObject), $this::REFERENCES) || !$message)
            return false;
        $chatMessage = new ChatMessage([
            'message' => $message,
            'sender_message_id' => $senderObject->{$senderObject->getKeyName()},
            'sender_message_table_name' => $senderObject->getTable(),
            'receiver_message_id' => $receiverObject->{$receiverObject->getKeyName()},
            'receiver_message_table_name' => $receiverObject->getTable(),
            'category' => $this::CATEGORY_MESSAGE,
            'job_id' => $job_id
        ]);
        if($imageSource){
            $chatAttachment = ChatAttachment::create([
                'attachment' => $imageSource
            ]);
            if(!$chatAttachment)
                return false;
            $chatMessage->chat_attachment_id = $chatAttachment->chat_attachment_id;
        }
        if(!$chatMessage->save())
            return false;
        return $chatMessage;
    }

    /**
     * Fetches system default notifier person
     * @return Person
     */
    public function getNotifier()
    {
        return Person::where('person_username', env('NOTIFICATOR_NAME'))->where('person_email', env('NOTIFICATOR_EMAIL'))->first();
    }

    /**
     * Gets all messages types
     * @param id
     * @return Int|Array
     */
    public function getMessageType($id = null)
    {
        $result = [
            $this::TYPE_JOB_STATUS_CHANGED => 'you appliance status has changed',
            $this::TYPE_JOB_APPLIED        => 'professional applied to one of your job(s)'
        ];
        if($id && !array_key_exists($id, $result))
            return false;
        return $id ? $result[$id] : $result;
    }

    /**
     * List messages of logged user
     * @param Array cahtMessageObjects
     * @param Int job_id
     * @param Int limit - default 100
     * @param Int page - default 1
     * @return EloquentPaginatedResultsOfChatMessage
     */
    public function listChatMessages($chatMessageObjects, $job_id = null, $limit = 100, $page = 1)
    {
        $queryObj = ChatMessage::leftJoin('chat_attachments', function($join){
            $join->on('chat_attachments.chat_attachment_id', '=', 'chat_messages.chat_attachment_id');
        })->orWhere(function($queryObj) use ($chatMessageObjects) {
            $queryObj->where('sender_message_id', $chatMessageObjects['sender']->{$chatMessageObjects['sender']->getKeyName()})
            ->where('sender_message_table_name', $chatMessageObjects['sender']->getTable());
        })->orWhere(function($queryObj) use ($chatMessageObjects) {
            $queryObj->where('receiver_message_id', $chatMessageObjects['sender']->{$chatMessageObjects['sender']->getKeyName()})
            ->where('receiver_message_table_name', $chatMessageObjects['sender']->getTable());
        });
        if($job_id)
            $queryObj->where('job_id', $job_id);
        $queryObj->orderBy('chat_messages.created_at', 'DESC');            
        return $limit ? $queryObj->paginate($limit) : $queryObj->get();
    }

    /**
     * Get all chat messages and separat it in an array with key as the job id
     * @param Array cahtMessageObjects
     * @param Int limit - default 100
     * @param Int page - default 1
     * @return ArrayOfChatMessage
     */
    public function listChatMessagesByJob($chatMessageObjects, $limit = 100, $page = 1)
    {
        $myChatMessages = $this->listChatMessages($chatMessageObjects, null, $limit, $page);
        $messagesByJob = [];
        foreach($myChatMessages as $chatMessage){
            $messagesByJob[$chatMessage->job_id][] = $chatMessage; 
        }
        return $messagesByJob;
    }
}
