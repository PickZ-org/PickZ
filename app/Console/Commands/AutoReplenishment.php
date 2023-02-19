<?php

namespace App\Console\Commands;

use App\Models\Location;
use App\Models\ProductUom;
use App\Models\Stock;
use App\Services\LocationService;
use App\Services\ReplenishmentService;
use App\Services\StockService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoReplenishment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:replenishment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks all pick locations for minimum quantities and creates replenishment tasks if possible and needed';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param ReplenishmentService $replenishmentService
     * @param LocationService $locationService
     * @param StockService $stockService
     * @return mixed
     * @throws \Exception
     */
    public function handle(
        ReplenishmentService $replenishmentService,
        StockService $stockService
    ) {
        $records = DB::table('product_uoms_locations')->where([
            'auto_replenish' => true
        ])->whereNotNull('minimum_quantity')->get();
        $bar = $this->output->createProgressBar($records->count());
        $bar->start();
        $this->line(' ' . $records->count() . ' Records to check');
        foreach ($records as $record) {
            $targetLocation = Location::findOrFail($record->location_id);
            $productUom = ProductUom::findOrFail($record->product_uom_id);

            $this->info('Checking stock levels for ' . $productUom->product->name . ' on location ' . $targetLocation->name);

            if ($targetLocation->type->id !== 2) {
                $this->error('Location is not a pick location, skipping...');
                break;
            }
            $currentStock = Stock::where([
                'product_uom_id' => $productUom->id,
                'location_id' => $targetLocation->id
            ])->first();
            $currentQuantity = (null !== $currentStock) ? $currentStock->future_max_quantity : 0;
            if ($currentQuantity < $record->minimum_quantity) {
                $this->info('Replenishment needed for ' . $productUom->product->name . ' on location ' . $targetLocation->name);
                // Stock levels on pick location are below minimum quantity, start replenishment
                $neededQuantity = $record->top_up_quantity - $currentQuantity;
                if ($neededQuantity <= 0) {
                    $this->error('Top up quantity is impossible for ' . $productUom->product->name . ' on location ' . $targetLocation->name);
                    break;
                }
                // Check if there is enough quantity on Bulk locations, if so, create replenishment tasks for this location
                if ($stockService->checkStockForQuantity($productUom->product, $productUom,
                    $neededQuantity, true, true)) {
                    $this->info('Creating replenishment tasks for ' . $productUom->product->name . ' on location ' . $targetLocation->name);
                    $bulkStocks = $replenishmentService->findBulkStocksForReplenishment($productUom, $neededQuantity);
                    foreach ($bulkStocks as $bulkStock) {
                        $replenishmentService->createReplenishmentTask($bulkStock['stock'], $targetLocation,
                            $bulkStock['quantity']);
                    }
                } else {
                    $this->info('Not enough on bulk for ' . $productUom->product->name . ' on location ' . $targetLocation->name);
                }
            }
            $bar->advance();
        }
        $bar->finish();
        $this->info(' Done!');
    }
}
