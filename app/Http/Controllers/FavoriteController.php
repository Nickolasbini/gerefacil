<?php

namespace App\Http\Controllers;

use App\Helpers\Functions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    /**
     * Create or Update
     * @param  <int> id for update
     * 
     * @return 
    */
    public function save()
    {

    }
      
    /**
     * List all 
     * @param  page <int> 
     * 
     * @return view
    */
    public function list()
    {
        $page  = $this->getParameter('page', 1);
        $limit = $this->getParameter('limit', 10);
        $favoriteObj = new Favorite();
        $favorites = $favoriteObj->list($page, $limit);
        return view('dashboard/favorite_views/favorite_list')->with([
            'favorites' => $favorites
        ]);
    }

    /**
     * Remove 
     * @param  id   to remove
     * 
     * @return
    */
    public function remove()
    {
        $favoriteId = $this->getParameter('favoriteId');
        if(!$favoriteId){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('an id is required'))
            ]);
        }
        $favoriteObj = Favorite::find($favoriteId);
        if(!$favoriteObj){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('favorite not found'))
            ]);
        }
        if($favoriteObj->user_id != $this->getLoggedUserId()){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('favorite is not yours'))
            ]);
        }
        $result = $favoriteObj->delete();
        if(!$result){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('an error occured, try again please'))
            ]);
        }
        Functions::translateAndSetToSession('favorite was removed');
        return json_encode([
            'success' => true,
            'message' => ucfirst(translate('favorite was removed'))
        ]);
    }
  
}

?>