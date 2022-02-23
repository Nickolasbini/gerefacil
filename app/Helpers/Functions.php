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

    // return url for view accordingly to enviroment at env
    public static function viewLink($path)
    {
        try {
            if(env('APP_ENV') == 'local'){
                return url($path);
            }else{
                return secure_url($path);
            }
        } catch (\Throwable $th) {
            abort(404);
        }
    }

    // order sent array as the $orderBy array sent
    // obs: works with indexed arrays only
    public static function orderArray($arrayToOrder, $orderBy)
    {
        $orderedArray = [];
        foreach($orderBy as $orderItem){
            $orderedArray[$orderItem] = $arrayToOrder[$orderItem];
        }
        return $orderedArray;
    }

    // return all categories avaliable for this user
    // $keyAttribute for key name and $attributeName for value attribue
    public static function getIndexedArray($keyAttribute = 'id', $attributeName = 'name', $objArray = [])
    {
        $indexedArray = [];
        foreach($objArray as $obj){
            $indexedArray[$obj->{$keyAttribute}] = $obj->{$attributeName};
        }
        return $indexedArray;
    }

    public static function formatDate($string, $format = null)
    {
        $format = ($format ? $format : env('DATE_FORMAT'));
        $date = new \DateTime((string)$string);
        return $date->format($format);
    }
}