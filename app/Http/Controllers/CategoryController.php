<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use CoffeeCode\Paginator\Paginator;

class CategoryController extends Controller 
{
    /**
     * Create or Update
     * @param  <int> id for update
     * 
     * @return 
    */
    public function save()
    {
        $id     = $this->getParameter('id');
        $name   = $this->getParameter('name');
        $categoryObj = new Category();
        if($id){
            $category = $categoryObj->find($id);
            if(!$category){
                return json_encode([
                    'success' => false,
                    'message' => ucfirst(translate('invalid'))
                ]);
            }
            $categoryObj = $category;
        }
        $result = Category::updateOrCreate(
            ['id' => $id],
            ['name' => $name, 'user_id' => $this->getLoggedUserId()]
        );
        if(!$result){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('an error occured, try again later'))
            ]);
        }
        $message = ($id ? ucfirst(translate('category updated')) : ucfirst(translate('category created')));
        return json_encode([
            'success' => true,
            'message' => $message,
            'id'      => $categoryObj->id
        ]);
    }
      
    /**
     * List all 
     * @param  page <int> 
     * 
     * @return view
    */
    public function list()
    {
        $page   = $this->getParameter('page', 1);
        $limit  = $this->getParameter('limit', 10);
        $filter = $this->getParameter('filter');

        $this->session->put('authUser-id', 1);

        $elements = [];
        $total = Category::where('user_id', $this->getLoggedUserId())->orWhere('user_id', null)->count();
        if($total < 0){
            return json_encode([
                'success' => false,
                'content' => $elements
            ]);
        }
        $paginator = new Paginator();
        $paginator->pager($total, $limit, $page);
        $categories = Category::where('id', '>', '0')->skip($paginator->offset())->take($paginator->limit())->get()->toArray();
        foreach($categories as $category){
            $elements[] = [
                'id' => $category->getId()
            ];
        }
        dd($elements);


    }

    /**
     * Remove 
     * @param  id   to remove
     * 
     * @return
    */
    public function remove()
    {
        
    }
}

?>