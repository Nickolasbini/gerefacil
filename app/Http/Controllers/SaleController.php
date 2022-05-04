<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;

class SaleController extends Controller
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
        $selectedStatus = $this->getParameter('status', 0);
        $orderObj = new Order();
        $parameters = [
            'criteria' => ($selectedStatus >= 0 && $selectedStatus <= 5 ? 'status' : ''),
            'value'    => $selectedStatus
        ];
        $orders = $orderObj->getAllCustomersOrders($parameters);
        $orderObj->insertProductOrderInventoryToOrderObj($orders);
        return view('dashboard/sale_views/sale_home')->with([
            'orders'         => $orders,
            'status'         => $orderObj->getAllStatusTranslated(),
            'selectedStatus' => $selectedStatus
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
        
    }
}
