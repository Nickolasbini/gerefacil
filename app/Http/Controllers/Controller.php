<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function __construct(Request $request) {
        $this->request = $request;
        $this->session = session();
    }

    function getSessionData()
    {
        return $this->request->session()->all();
    }

    function getParameters()
    {
        return $this->request->all();
    }

    function getParameter($parameterName, $defaultValue = null)
    {
        if(is_null($this->request->{$parameterName})){
            $this->setParameter($parameterName, $defaultValue);
        }
        return $this->request->{$parameterName};
    }

    function setParameter($parameterName, $defaultValue = null)
    {
        $this->request->merge([$parameterName => $defaultValue]);
    }
}