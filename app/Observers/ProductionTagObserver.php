<?php

namespace App\Observers;

use App\ProductionTag;

class ProductionTagObserver
{
    /**
     * Handle the production tag "created" event.
     *
     * @param  \App\ProductionTag  $productionTag
     * @return void
     */
    public function created(ProductionTag $productionTag)
    {
        //


        $oldtags =  ProductionTag::where('tags_group', $productionTag->tags_group)
        ->whereNull('end_time')
        ->orWhere('end_time', '')
        ->orWhere('end_time', '>', date('Y-m-d H:i:s'))
        ->get();
        if ($oldtags) {
            foreach ($oldtags as $oldtag) {
              if($oldtag->id <>$productionTag->id ){
                $oldtag->end_time = date('Y-m-d H:i:s');
                $oldtag->save();
              }

            }
        }
    }

    /**
     * Handle the production tag "updated" event.
     *
     * @param  \App\ProductionTag  $productionTag
     * @return void
     */
    public function updated(ProductionTag $productionTag)
    {
        //
    }

    /**
     * Handle the production tag "deleted" event.
     *
     * @param  \App\ProductionTag  $productionTag
     * @return void
     */
    public function deleted(ProductionTag $productionTag)
    {
        //
    }

    /**
     * Handle the production tag "restored" event.
     *
     * @param  \App\ProductionTag  $productionTag
     * @return void
     */
    public function restored(ProductionTag $productionTag)
    {
        //
    }

    /**
     * Handle the production tag "force deleted" event.
     *
     * @param  \App\ProductionTag  $productionTag
     * @return void
     */
    public function forceDeleted(ProductionTag $productionTag)
    {
        //
    }
}
