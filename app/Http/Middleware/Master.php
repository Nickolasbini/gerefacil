<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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
        $this->getUserLanguageToSession();
        return $next($request);
    }

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
}
