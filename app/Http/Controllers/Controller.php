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

    function isAdmin()
    {
        if($this->session->get('authUser-is_admin')){
            return true;
        }else{
            return null;
        }
    }

    function isLogged()
    {
        if($this->session->get('authUser-id')){
            return true;
        }else{
            return null;
        }
    }

    function getLoggedUserId()
    {
        return $this->session->get('authUser-id');
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

    // returns the master admin
    function getMasterAdmin()
    {
        return \App\Models\User::where('master_admin', 1);
    }

    function getMasterAdminId()
    {
        return $this->session->get('master_admin-id');
    }

    // return all categories avaliable for this user
    // $keyAttribute for key name and $attributeName for value attribue
    public function getIndexedArray($keyAttribute = 'id', $attributeName = 'name', $objArray = [])
    {
        $indexedArray = [];
        foreach($objArray as $obj){
            $indexedArray[$obj->{$keyAttribute}] = $obj->{$attributeName};
        }
        return $indexedArray;
    }
}