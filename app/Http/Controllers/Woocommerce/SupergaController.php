<?php

namespace App\Http\Controllers\Woocommerce;

use App\Http\Controllers\Controller;
use Automattic\WooCommerce\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupergaController extends Controller
{
    protected $woocommerce;

    public function __construct()
    {
        $this->woocommerce = new Client(
            'https://www.superga.cl',
            'ck_369f4ee9fb12aea100c879a4f6f8f496625d3f58',
            'cs_e1a78967eaaf5accda94a06351ccdc3561e773f2',
            [
                'version' => 'wc/v3',
            ]
        );
    }

    public function index()
    {
        return Inertia::render('woocommerce/Superga');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'orderId' => ['required', 'integer'],
        ]);

        $orderId = $validated['orderId'];

        try {
            $this->woocommerce->put("orders/$orderId", [
                'status' => 'completed',
            ]);

            return redirect()
                ->route('superga.change.status')
                ->with('success', "Pedido #$orderId actualizado a COMPLETADO correctamente.");

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al actualizar el pedido: '.$e->getMessage());
        }
    }
}
