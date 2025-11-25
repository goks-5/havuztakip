<?php

use Illuminate\Http\Request;
use App\Http\Middleware\EnsureTokenIsValid;
use App\Http\Middleware\CheckCompanyToken;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\DeviceExcelController;
use App\Http\Controllers\DeviceTagsApiController;

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

Route::get('/enerji/read/tag_value/{device_id}/{tag_id}/{date?}', [\App\Http\Controllers\DeviceTagsApiController::class, 'seriesByDeviceIdTag'])
  ->where([
    'device_id' => '[0-9]+',
    'tag_id'    => '\d{1,3}',          // 0–999
    'date'      => '\d{4}-\d{2}-\d{2}' // YYYY-MM-DD (opsiyonel)
  ])
  ->middleware([\App\Http\Middleware\CheckCompanyToken::class]);

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
Route::get('/enerji/read/tag_value_cost/{device_id}/{tag_id}/{date1}/{date2}', 'DeviceTagsApiController@costBetweenDates');