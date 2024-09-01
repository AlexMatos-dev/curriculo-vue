<?php

namespace App\Helpers;


class WebResponse{
    /**
     * Returns to login page with message - Saves message to session and render login view 
     * @param String message
     * @param String messageType
     * @return Void
     */
    public static function returnMessageToLoginPage($message = '', $messageType = 'error')
    {
        Session()->put('web_message', $message);
        Session()->put('web_message_type', $messageType);
        exit(view('login')->with(['translations']));
    }

    /**
     * Returns a json response 
     * @param String message
     * @param Boolean success
     * @return JSON
     */
    public static function returnJson($message = '', $success = false)
    {
        exit(json_encode([
            'success' => $success,
            'message' => $message
        ]));
    }
}