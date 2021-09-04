<?php

namespace App\Providers;

use TCG\Voyager\Facades\Voyager;
use App\FormFields\AutoComplateField;
use App\FormFields\MultipleTextField;
use App\FormFields\QueryTextField;
use App\FormFields\DashboardField;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        Voyager::addFormField(AutoComplateField::class);
          Voyager::addFormField(MultipleTextField::class);
            Voyager::addFormField(QueryTextField::class);
              Voyager::addFormField(DashboardField::class);

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Voyager::addAction(\App\Actions\DeviceAction::class);
        Voyager::addAction(\App\Actions\ReportAction::class);
    }
}
