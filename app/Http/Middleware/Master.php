<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Intervention\Validation\Rules\HtmlClean;
use App\Helpers\Functions;

class Master extends \App\Http\Controllers\Controller
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $this->sanitazeValues($request);
        $this->setMasterAdminData();
        $this->getUserLanguageToSession();
        Self::createViewMessageSession();
        $this->createBaseCategories();
        $this->setURI($request);
        return $next($request);
    }

    // tries to capture user language to set to session and a cookie
    public function getUserLanguageToSession()
    {
        $possible = ['pt', 'en', 'es'];
        $matches = [
            'pt' => ['pt-BR'],
            'en' => ['en-US']
        ];

        if(!array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)){
            $languageOfUser = env('USER_LANGUAGE');
        }else{
            $acceptedLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            $arrayWithData = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $languageOfUser = env('USER_LANGUAGE');
            foreach($arrayWithData as $data){
                $position = strpos($data, ';q=');
                if(is_numeric($position)){
                    $languageISO = substr($data, 0, $position);
                }else{
                    $languageISO = $data;
                }
                if(in_array($languageISO, $possible)){
                    $languageOfUser = $languageISO;
                    break;
                }else{
                    foreach($matches as $isoOfLanguage => $match){
                        if(in_array($languageISO, $matches[$isoOfLanguage])){
                            $languageOfUser = $isoOfLanguage;
                            break;
                        }
                    }
                }
            }
        }
        if(!$this->request->cookie('userLanguage')){
            \App\Helpers\Functions::setCookie('userLanguage', $languageOfUser);
        }
        $this->session->put('userLanguage', $languageOfUser);
        return $languageOfUser;
    }

    // gather masterAdmin data to set some attributes to session, also creates a master_admin
    public function setMasterAdminData()
    {
        if(session()->get('masterAdmin-id')){
            return;
        }
        $user = \App\Models\User::where('master_admin', 1)->get();
        if($user->count() < 1){
            $user = \App\Models\User::create(['name' => 'masterAdmin', 'email' => 'gerefacil@gmail.com', 'password' => hash('sha256', env('MAIL_PASSWORD')), 'is_admin' => 1, 'master_admin' => 1]);
        }else{
            $user = $user[0];
        }
        $userData = $user->toArray();
        $doNotSave = ['email', 'password', 'email_verified_at', 'current_team_id', 'profile_photo_path', 'cpf', 'address', 'cep', 'sex', 'dateOfBirth', 'license_number', 'created_at', 'updated_at'];
        $session = session();
        foreach($userData as $attributeName => $attribute){
            if(!in_array($attributeName, $doNotSave)){
                $session->put('masterAdmin-'.$attributeName, $attribute);
            }
        }
    }

    public function sanitazeValues($request)
    {
        
    }

    // create field 'viewMessage' and 'messageOption' at session
    public static function createViewMessageSession()
    {
        if(!session()->has('viewMessage')){
            Functions::emptyTranslateSession();
        }
    }

    // create at session a uri data reference
    public function setURI($request)
    {
        $this->session->put('uri', $request->path());
    }

    // creates the base categories in case they are not yet created
    public function createBaseCategories()
    {
        $categoriesFile = json_decode(file_get_contents(asset('files/defaultCategories.json')), true);
        if(!$categoriesFile){
            return;
        }
        if(session()->get('alreadyCheckedCategoriesFile')){
            return;
        }
        $masterAdmin = \App\Models\User::where('master_admin', 1)->limit(1)->get();
        if(count($masterAdmin) < 1){
            return;
        }
        $idToUse = $masterAdmin[0]->id;
        foreach($categoriesFile['categories'] as $categoryName){
            $categoryObj = new \App\Models\Category();
            if(count( $categoryObj::where('name', $categoryName)->get() ) > 0){
                continue;
            }
            $categoryObj->name    = $categoryName;
            $categoryObj->user_id = $idToUse;
            $categoryObj->save();
        }
        session()->put('alreadyCheckedCategoriesFile', true);
    }
}
