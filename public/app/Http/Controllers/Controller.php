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
}
