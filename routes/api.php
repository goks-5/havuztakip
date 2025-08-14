<?php

use Illuminate\Http\Request;
use App\Http\Middleware\EnsureTokenIsValid;
use App\Http\Middleware\CheckCompanyToken;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\DeviceExcelController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
if(env('REDIRECT_HTTPS')){
    URL::forceScheme('https');
}

Route::get('/showrequest','Api@tests');
Route::post('/showrequest','Api@tests');
Route::post('/write', 'Api@write')->middleware([EnsureTokenIsValid::class]);
Route::get('/read/devices', 'Api@devices')->middleware([CheckCompanyToken::class]);
Route::get('/read/device/{device_id}', 'Api@device')->middleware([CheckCompanyToken::class]);
Route::get('/read/device/{device_id}/{index}', 'Api@tag')->middleware([CheckCompanyToken::class]);
Route::post('/write/device/{device_id}/{index}/{value}', 'Api@writeTag')->middleware([CheckCompanyToken::class]);

Route::get('excel/devices', [DeviceExcelController::class, 'index']);
Route::get('excel/device/{id}/data/{dataId}', [DeviceExcelController::class, 'data']);
Route::get('excel/device-daily-latest', [DeviceExcelController::class, 'dailyLatest']);
Route::get('excel/device-daily', [DeviceExcelController::class, 'dailyByDate']);

Route::get('/read/devices-tags-values', 'DeviceTagsApiController@index')
     ->middleware([\App\Http\Middleware\CheckCompanyToken::class]);
