<?php

namespace App\Http\Controllers\Woocommerce;

use App\Http\Controllers\Controller;
use App\Models\OrderStatusUpdate;
use Inertia\Inertia;

class PendingOrdersController extends Controller
{
    public function index()
    {

        $pendingOrders = OrderStatusUpdate::paginate(10);

        return Inertia::render('woocommerce/PendingOrders', [
            'pendingOrders' => $pendingOrders,
        ]);
    }
}
