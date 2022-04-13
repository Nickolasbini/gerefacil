<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;

class AuthenticatedUserActions extends \App\Http\Controllers\Controller
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
        $this->insertAuthenticatedUserDataToSession($request->user());

        if($this->separateUsers($request)){
            return redirect('/user/profile');
        }

        return $next($request);
    }

    /**
     * Puts all attributes of User object into the current session.
     *
     * @param  user object
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function insertAuthenticatedUserDataToSession($userObject)
    {
        foreach($userObject->getAttributes() as $keyName => $data){
            if($keyName == 'is_admin'){
                $data = ($data == 1 ? true : false);
            }
            $this->session->put('authUser-' . $keyName, $data);
        }
        $this->session->put('userLanguage', env('USER_LANGUAGE'));
    }

    /**
     * Responsible by allowing ADMIN only to routes
     * @return <nill>
     */
    public Function separateUsers($request)
    {
        $exceptionsForNonAdmins = [
            '/user/profile',
            'logout'
        ];
        $requestURI = $request->getRequestUri();
        if($this->isAdmin()){
            return;
        }
        if($requestURI == '/dashboard'){
            return true;
        }
        // check the exception
        if(in_array($requestURI, $exceptionsForNonAdmins)){
            return;
        }
        return true;
    }
}
