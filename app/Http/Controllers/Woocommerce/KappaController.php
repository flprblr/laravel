<?php

namespace App\Http\Controllers\Woocommerce;

use App\Http\Controllers\Controller;
use Automattic\WooCommerce\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KappaController extends Controller
{
    protected $woocommerce;

    public function __construct()
    {
        $this->woocommerce = new Client(
            'https://www.kappa.cl',
            'ck_8501b785175eb486036c751b383361dcbc8dc2c',
            'cs_ca4c6dc9216bb4c8b21d48b07f1dbc990fa7bf4',
            [
                'version' => 'wc/v3',
            ]
        );
    }

    public function index()
    {
        return Inertia::render('woocommerce/Kappa');
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
                ->route('kappa.change.status')
                ->with('success', "Pedido #$orderId actualizado a COMPLETADO correctamente.");

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al actualizar el pedido: '.$e->getMessage());
        }
    }
}
