<?php

namespace App\Observers;
use Illuminate\Support\Facades\DB;
use App\StockMovement;

class StockMovementObserver
{
    /**
     * Handle the production tag "created" event.
     *
     * @param  \App\StockMovement  $productionTag
     * @return void
     */
    public function created(StockMovement $stockMovement)
    {
        $this->countAll();
    }

    /**
     * Handle the production tag "updated" event.
     *
     * @param  \App\StockMovement  $productionTag
     * @return void
     */
    public function updated(StockMovement $stockMovement)
    {
        $this->countAll();
    }

    /**
     * Handle the production tag "deleted" event.
     *
     * @param  \App\StockMovement  $productionTag
     * @return void
     */
    public function deleted(StockMovement $stockMovement)
    {
        $this->countAll();
    }

    /**
     * Handle the production tag "restored" event.
     *
     * @param  \App\StockMovement  $productionTag
     * @return void
     */
    public function restored(StockMovement $stockMovement)
    {
        $this->countAll();
    }

    /**
     * Handle the production tag "force deleted" event.
     *
     * @param  \App\StockMovement  $productionTag
     * @return void
     */
    public function forceDeleted(StockMovement $stockMovement)
    {
        $this->countAll();
    }

    private function countAll(){
                
        DB::statement("DROP TABLE IF EXISTS parts_counts;");
        DB::statement("CREATE table parts_counts as select `stock_movements`.`replacement_part_id` AS `replacement_part_id`,sum(`stock_movements`.`stock`) AS `count` from `stock_movements` where isnull(`stock_movements`.`deleted_at`) group by `stock_movements`.`replacement_part_id` ");
    }

}
