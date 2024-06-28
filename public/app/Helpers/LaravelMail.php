<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class LaravelMail extends Mailable
{
    public $recipients;
    public $subject;
    public $body;
    public $attachments;

    public function __construct($recipients = [], $subject = '', $body = '', $attachments = [])
    {
        $this->recipients = !is_array($recipients) ? [$recipients] : $recipients;
        $this->subject = $subject;
        $this->body = (string)$body;
        $this->attachments = $attachments;
        $this->ensureConfig();
    }

    /**
     * Builds email by setting subjects and attachments if there're any
     */
    public function build()
    {
        $email = $this->subject($this->subject)->html($this->body);
        if(!empty($this->attachments)){
            foreach ($this->attachments as $filePath) {
                $email->attach($filePath);
            }
        }
        return $email;
    }

    /**
     * Sets all required configurations for mail accordingly to values on .env 
     */
	private function ensureConfig()
	{
		config(['mail.driver' => env('MAIL_MAILER')]);
        config(['mail.host' => env('MAIL_HOST')]);
        config(['mail.port' => env('MAIL_PORT')]);
        config(['mail.from.address' => env('MAIL_FROM_ADDRESS')]);
        config(['mail.from.name' => env('MAIL_FROM_NAME')]);
        config(['mail.encryption' => env('MAIL_ENCRYPTION')]);
        config(['mail.username' => env('MAIL_USERNAME')]);
        config(['mail.password' => env('MAIL_PASSWORD')]);
	} 

    /**
     * Sends message to informed emails
     * @return Array - Schema: ['sucess' => bool, 'message' => string]
     */
    public function sendMail()
    {
        try {
            Mail::to($this->recipients)->send($this);
            return [
				'success' => true,
				'message' => 'mail sent with success'
			];
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => "Error: {$th->getMessage()}"
            ];
        }
    }
}