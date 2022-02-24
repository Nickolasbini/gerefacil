<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Serial;
use App\Models\User;

class UserController extends Controller
{
    // return profile picture as base64
    public function getProfilePhoto()
    {
        $profilePath = $this->session->get('authUser-profile_photo_path');
        if(!$profilePath){
            return null;
        }
        $path = storage_path('app/public/' . $profilePath);
        if(file_exists($path)){
            return base64_encode(file_get_contents($path));
        }else{
            return null;
        }
    }

    // validates sent CPF
    public function validateCPF()
    {
        $cpf = $this->getParameter('cpf');
        if(!$cpf){
            return json_encode([
                'success' => false,
                'message' => 'a value is required'
            ]);
        } 
        $user = new User();
        $result = $user->validateCPF($cpf);
        return json_encode([
            'success' => true,
            'isValid' => $result
        ]);
    }

    // gets CEP data such as street, city and country
    public function getCEPData()
    {
        $cep = $this->getParameter('cep');
        if(!$cep){
            return json_encode([
                'success' => false,
                'message' => 'a value is required'
            ]);
        } 
        $user = new User();
        $result = $user->getCEPData($cep);
        return json_encode([
            'success'   => true,
            'message'   => 'finished',
            'hasResult' => ($result ? true : false),
            'content'   => $result
        ]);
    }

    // checks if serial exists
    public function checkSerial()
    {
        $serialCode = $this->getParameter('serial');
        $email      = $this->getParameter('email');
        if(!$serialCode){
            return json_encode([
                'success' => false,
                'message' => 'a value is required'
            ]);
        } 
        $serial = new Serial();
        $result = $serial->firstWhere('serial', $serialCode);
        if($result){
            $result->email_using = $email;
            $result->isInUse = 1;
            $result->save();
        }
        return json_encode([
            'success' => true,
            'message' => 'finished',
            'isValid' => ($result ? true : false)
        ]);
    }

    // cleans session ViewMessage
    public function cleanViewMessage()
    {
        return session()->put('viewMessage', null);
    }
}
