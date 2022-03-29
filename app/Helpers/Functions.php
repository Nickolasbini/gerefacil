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
    public static function viewLink($path, $ignoreException = false)
    {
        // when route workds only like this, maybe because there are two routes with same name but one is GET and other is POST
        $exceptions = ['dashboard/product/save'];
        if(in_array($path, $exceptions) && !$ignoreException)
            return;
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

    // translate $text and set it to session as 'viewMessage'
    public static function translateAndSetToSession($text, $typeOfMessage = 'success')
    {
        session()->put('viewMessage', ucfirst(translate($text)));
        session()->put('messageType', $typeOfMessage);
    }

    // translate $text and set it to session as 'viewMessage'
    public static function emptyTranslateSession()
    {
        session()->put('viewMessage', null);
        session()->put('messageType', null);
    }

    // redirect to uri
    public static function redirectToURI($customURI = null)
    {
        $uriToUse = $customURI ? $customURI : session()->get('uri');
        return redirect($uriToUse);
    }

    /**
     * store files at STORAGE accordingly
     * @param <array>  keys: <string> 'name' (bla.$extension), <string> 'tmp_name'
     * @param <string> the path to store data
     * @return nill
     */
    public static function saveFiles($files = null, $path = 'app/archives/')
    {
        $files = ($files ? $files : $_FILES);
        // base path
        $storagePath = storage_path($path);
        // create required folders
        $pathToFolders = storage_path() . '/';
        foreach(explode('/', $path) as $folder){
            $pathToFolders .= $folder .'/';
            if($folder == '')
                continue;
            if(!file_exists($pathToFolders))
                mkdir($pathToFolders);
        }
        // store files
        foreach($_FILES as $file){
            for($i = 0; $i < count($file['name']); $i++){
                $name      = $file['name'][$i];
                $tmp       = $file['tmp_name'][$i];
                if($name != ''){
                    file_put_contents($storagePath . $name, file_get_contents($tmp));
                }
            }
        }
    }

    // formats value with the correct currency type
    public static function formatMoney($value)
    {
        $currencyLang = env('CURRENCY_LANG');
        $currencyType = env('CURRENCY_TYPE');
        $money = null;
        switch($currencyLang){
            case 'BRL':
                $money = 'R$' . str_replace('.', ',', $value);
            break;
            case 'USD':
                $money = '$' . str_replace('.', ',', $value);
            break;
            default:
                $money = 'R$' . str_replace('.', ',', $value);
            break;
        }
        return $money;
    }

    public static function generateHash($value = null, $directorySensitive = false, $maxLength = 30)
    {
        $value  = ($value ? $value : (new \DateTime)->format('Y-m-d h:i:s'));
        $hashed =  \Illuminate\Support\Facades\Hash::make($value);
        $hashed = substr($hashed, 0, $maxLength);
        return ($directorySensitive ? str_replace(['.', '/', '-'], '', $hashed) : $hashed);
    }

    // prepare string to be saved on decimal field at DB
    public static function parsePriceToDB($price)
    {
        $price = str_replace(['R$', '$'], '', $price);
        $price = trim(str_replace(',', '.', $price));
        $price = preg_replace('/\s+/', '', $price);
        return (float)$price;
    }

    // shorten a text including '...' at the end if needed
    public static function shortenText($text, $maxLength = 200)
    {
        if(strlen($text) > $maxLength){
            $newText = substr($text, 0, $maxLength);
            $text = $newText . ' ...'; 
        }
        return $text;
    }

    // return data of adminConfig.json
    /*
     * adminMessage is used when there's a message to be displayed for everyone, the 'text' is the message, 'from' and 'to' are the period for this to be displayed
     * note: will only be displayed when 'login' is done
    */
    public static function adminRelatedDataArray()
    {
        $path = storage_path('app/admin');
        if(!is_dir($path)){
            mkdir($path);
        }
        $filePath = $path . '/adminConfig.json';
        $configFileStructure = [
            'whatsApp'     => '',
            'whatsMessage' => '',
            'facebook'     => '',
            'instagram'    => '',
            'email'        => '',
            'adminMessage' => [
                'text' => '',
                'from' => '',
                'to'   => ''
            ]
        ];
        if(!file_exists($filePath)){
            file_put_contents($filePath, json_encode($configFileStructure));
        }
        return json_decode(file_get_contents($filePath), true);
    }
}