<?php
namespace App\Helpers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;

// can be called anywhere by \AppHelper
class Functions
{
    // sets a cookie if it does no exist
    // default expiration time is 2 days
    public static function setCookie($cookieName = null, $cookieData = null, $expirationTime = 2880)
    {
        if(!$cookieName || !$cookieData)
            return false;
        $response = new Response('Set Cookie');
        $response->withCookie(cookie($cookieName, $cookieData, $expirationTime));
        return $response;
    }

    // gets a cookie
    public static function getCookie($cookieName, Request $request){
        $value = $request->cookie($cookieName);
        return $value;
     }
}