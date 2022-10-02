<?php
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use TCG\Voyager\Facades\Voyager;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


if(env('REDIRECT_HTTPS')){
    URL::forceScheme('https');
}


Route::get('/admin', function () {
    return Redirect::to('/', 301);
});

Route::post('/autocomplate', 'Ajax@AutoComplate');
Route::get('/devicelist', 'Ajax@DeviceList')->name('devicelist');
Route::get('/server_info', 'Controller@serverInfo')->name('serverInfo');
Route::get('/deviceonline', 'Ajax@DeviceOnline')->name('deviceonline');
Route::post('/ajax/dahboard_tool', 'Ajax@dahboardTool')->name('dahboardTool');
Route::post('/ajax/boardAction', 'Ajax@boardAction')->name('boardAction');
Route::get('/ajax/butondata', 'Ajax@manuelAjax')->name('butondata');

Route::post('/ajax/dashboardTagsAdd', 'Ajax@dashboardTagsAdd')->name('TagsAdd');
Route::post('/ajax/toolStyle', 'Ajax@toolStyle')->name('toolStyle');
Route::get('/ajax/dashboardTagEnd', 'Ajax@dashboardTagEnd')->name('end_tag');
Route::post('/ajax/calculate', 'Ajax@calculate')->name('calculate');
Route::post('/ajax/faults_actions', 'Faults@actions')->name('faultsActions');
Route::group(['prefix' => ''], function () {
    
    Route::get('/raporlar/excel/{id}', ['uses' => 'Reports@excel', 'as' => 'report_excel']);
    Route::get('/custom-reports/excel/{id}', ['uses' => 'CustomReports@excel', 'as' => 'custom_report_excel']);
    Route::get('/update', 'Controller@updateFromGit')->name('updateFromGit');
    Route::get('/dashboard', 'Dashboards@ajaxdata')->name('dashboarddata');
    Route::get('/dashboard/{id}', 'Dashboards@dashboard')->name('dashboardnew');
    Route::get('/firmadegistir/{id?}', 'Companies@switch')->name('switch_company');
    Route::get('/gorevler/{status?}', ['uses' => 'Maintenances@tasks', 'as' => 'tasks']);
    Route::post('/gorevler', ['uses' => 'Maintenances@addEdit', 'as' => 'task_edit']);
    Route::get('/cihazlar/sanal/{id?}', ['uses' => 'Devices@addVirtual', 'as' => 'sanalekle']);
    Route::get('/cihazlar/reset_token/{id}', ['uses' => 'Devices@ResetToken', 'as' => 'reset_token']);
    Route::post('/cihazlar/sanal', ['uses' => 'Devices@saveVirtual', 'as' => 'sanalkaydet']);
    Route::get('/cihazlar/manuel', ['uses' => 'Devices@addManuel', 'as' => 'manuelekle']);
    Route::post('/cihazlar/manuelAdd', ['uses' => 'Devices@addManuelData', 'as' => 'manuelAdd']);
    Route::get('/cihazlar/remote_add', ['uses' => 'Devices@addRemote', 'as' => 'remoteAdd']);
    Route::get('/cihazlar/dosab/{id?}', ['uses' => 'Devices@addDosab', 'as' => 'dosabekle']);
    Route::post('/cihazlar/dosab', ['uses' => 'Devices@saveDosab', 'as' => 'dosabkaydet']);
    Route::get('/cihazlar/veriler', ['uses' => 'Devices@DevicesDatas', 'as' => 'veriler']);
    Route::post('/cihazlar/veriler', ['uses' => 'Devices@DeviceDatasSearch', 'as' => 'cihazverilerajax']);
    Route::get('/cihazlar/veriler/{id}', ['uses' => 'Devices@DeviceDatas', 'as' => 'cihazveriler']);
    Voyager::routes();
    // Route::get('/ekran', ['uses' => 'Dashboards@index',   'as' => 'voyager.dashboard']);
});
