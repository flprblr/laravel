<?php

namespace App\Http\Controllers\Woocommerce;

use App\Http\Controllers\Controller;
use App\Models\OrderStatusUpdate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StreetmachineController extends Controller
{
    public function index()
    {
        return Inertia::render('woocommerce/Streetmachine');
    }

    public function update(Request $request)
    {
        // Ahora el textarea se llama "orders"
        $validated = $request->validate([
            'orders' => ['required', 'string', 'max:20000'],
        ]);

        $raw = $validated['orders'];

        // Separa por saltos de línea, comas o espacios
        $tokens = preg_split('/[\r\n,\s]+/u', $raw, -1, PREG_SPLIT_NO_EMPTY);

        // Filtra solo numéricos
        $orderNumbers = collect($tokens)
            ->map(fn ($t) => trim($t))
            ->filter(fn ($t) => preg_match('/^\d+$/', $t) === 1)
            ->map(fn ($t) => (int) $t)
            ->unique()
            ->values();

        if ($orderNumbers->isEmpty()) {
            return back()->with('error', 'No se encontraron números de pedido válidos.');
        }

        // Evita insertar pedidos ya existentes para este ecommerce
        $existing = OrderStatusUpdate::query()
            ->where('ecommerce', 'streetmachine.cl')
            ->whereIn('order_number', $orderNumbers)
            ->pluck('order_number')
            ->all();

        $toInsert = $orderNumbers->reject(fn ($n) => in_array($n, $existing))->values();

        if ($toInsert->isEmpty()) {
            return back()->with('success', 'Todos los pedidos ya estaban registrados.');
        }

        $now = now();
        $rows = $toInsert->map(fn ($num) => [
            'ecommerce' => 'streetmachine.cl',
            'status' => 'Pendiente',
            'order_number' => $num,
            'attempts' => 0,
            'last_error' => null,
            'processed_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        OrderStatusUpdate::insert($rows);

        $inserted = count($rows);
        $skipped = count($existing);

        return back()->with('success', "Pedidos guardados: {$inserted}. Omitidos: {$skipped}.");
    }
}
