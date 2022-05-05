<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;

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
    public function list($status = null, $from = null, $to = null)
    {
        $orderObj = new Order();
        $parameters = [
            'status' => $status,
            'from'   => $from,
            'to'     => $to
        ];
        if($from && !$to)
            $parameters['to']   = Carbon::now()->format('Y-m-d');
        if(!$from && $to)
            $parameters['from'] = $parameters['to']; 
        $orders = $orderObj->getAllCustomersOrders($parameters);
        $orderObj->insertProductOrderInventoryToOrderObj($orders);
        return view('dashboard/sale_views/sale_home')->with([
            'orders'         => $orders,
            'status'         => $orderObj->getAllStatusTranslated(),
            'selectedStatus' => $parameters['status'],
            'from'           => $parameters['from'],
            'to'             => $parameters['to']
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
