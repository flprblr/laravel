<?php

namespace App\Console\Commands;

use App\Models\OrderStatusUpdate;
use Automattic\WooCommerce\Client;
use Illuminate\Console\Command;

class ProcessStreetmachineOrders extends Command
{
    protected $signature = 'streetmachine:process';

    protected $description = 'Procesa hasta 10 pedidos pendientes o fallidos de streetmachine.cl y los marca como completed en WooCommerce.';

    protected function getWoocommerceClient(): Client
    {
        return new Client(
            'https://www.streetmachine.cl',
            'ck_7f8ce200c1adadb3dc70d10c69437b3250293b5e',
            'cs_ca560fa45d868e0b291b1e858fc33425dd10d471',
            ['version' => 'wc/v3']
        );
    }

    public function handle(): int
    {
        $jobs = OrderStatusUpdate::query()
            ->where('ecommerce', 'streetmachine.cl')
            ->whereIn('status', ['Pendiente', 'Error'])
            ->orderBy('attempts')
            ->orderBy('id')
            ->limit(10)
            ->get();

        if ($jobs->isEmpty()) {
            $this->info('No hay pedidos para procesar.');

            return self::SUCCESS;
        }

        $wc = $this->getWoocommerceClient();
        $this->info("Procesando {$jobs->count()} pedidos de streetmachine.cl…");

        foreach ($jobs as $job) {
            try {
                $wc->put("orders/{$job->order_number}", ['status' => 'completed']);

                $job->status = 'Actualizado';
                $job->processed_at = now();
                $job->last_error = null;
                $job->attempts = $job->attempts + 1;
                $job->save();

                $this->line("✔ Pedido #{$job->order_number} → COMPLETED");
            } catch (\Throwable $e) {
                $job->status = 'failed';
                $job->attempts = $job->attempts + 1;
                $job->last_error = mb_substr($e->getMessage(), 0, 1000);
                $job->save();

                $this->warn("✖ Pedido #{$job->order_number} → FAILED ({$e->getMessage()})");
            }
        }

        return self::SUCCESS;
    }
}
