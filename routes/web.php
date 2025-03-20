<?php
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use TCG\Voyager\Facades\Voyager;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeklifController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BillsController;
use App\Http\Controllers\MeasurementController;

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

Route::post('/save-device-data', [DashboardTool::class, 'saveDeviceData']);
Route::post('/add-dashboard-visual', [DashboardTool::class, 'addDashboardVisual']);

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
    Route::get('/dashboard/{source}/{target}', 'Dashboards@copy')->name('dashboardcopy');
    Route::get('/firmadegistir/{id?}', 'Companies@switch')->name('switch_company');
    Route::get('/gorevler/{status?}', ['uses' => 'Maintenances@tasks', 'as' => 'tasks']);
    Route::post('/gorevler', ['uses' => 'Maintenances@addEdit', 'as' => 'task_edit']);
    Route::get('/cihazlar/sanal/{id?}', ['uses' => 'Devices@addVirtual', 'as' => 'sanalekle']);
    Route::get('/cihazlar/infos/{id?}', ['uses' => 'Devices@deviceInfos', 'as' => 'deviceInfos']);
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
    Route::get('/dinamik-rapor', function () {
        $devices = \App\Device::all()->map(function ($device) {
            return [
                'id' => $device->id,
                'name' => $device->name,
                'tags' => json_decode($device->tags, true) ?? []
            ];
        });
    
        return view('vendor.voyager.dinamik-rapor.browse', compact('devices'));
    })->name('dinamik-rapor.browse');
        
    Route::get('/get-filtered-data', [ChartController::class, 'getFilteredData']);    
    Route::get('/get-fields', function () {
        $fields = \App\Field::all(['id', 'name']); // 'fields' tablosundan 'id' ve 'name' alanlarını alıyoruz
        return response()->json($fields);
    });
    Route::get('/get-devices', function () {
        $devices = \App\Device::all(['id', 'name']); // 'devices' tablosundan 'id' ve 'name' alanlarını alıyoruz
        return response()->json($devices);
    });
    Route::get('/get-field-list', [FieldController::class, 'getFields']);
    Route::get('/get-field-filtered-data', [ChartController::class, 'getResourceTotals']);    
    
    // Firma Tablosu Browse Route
    Route::get('/firma-tablosu', function () {
        $company = \App\Firm::all();        // Doğru tablo adı kullanıldı
        return view('vendor.voyager.firma-tablosu.browse', compact('company'));
    })->name('firma-tablosu.browse');
    
    Route::post('/firma-tablosu/store', [CompanyController::class, 'store'])->name('firma-tablosu.store');
    Route::delete('/firma-tablosu/destroy/{id}', [CompanyController::class, 'destroy'])->name('firma-tablosu.destroy');
    Route::put('/firma-tablosu/update/{id}', [CompanyController::class, 'update'])->name('firma-tablosu.update');

    // Kullanıcı Tablosu Rotaları (Değiştirildi)
    Route::get('/kullanici-tablosu', [UserController::class, 'index'])->name('kullanici-tablosu.index');
    Route::post('/kullanici-tablosu/store', [UserController::class, 'store'])->name('kullanici-tablosu.store');
    Route::put('/kullanici-tablosu/update/{id}', [UserController::class, 'update'])->name('kullanici-tablosu.update');
    Route::delete('/kullanici-tablosu/destroy/{id}', [UserController::class, 'destroy'])->name('kullanici-tablosu.destroy');

    Route::get('/teklif-hazirla', function () {
        $offers = \App\Offer::all();
        $users = \App\UserAccount::pluck('user_name', 'id');
        return view('vendor.voyager.teklif-hazirla.browse', compact('offers', 'users'));
    })->name('teklif-hazirla.browse');   
     
    Route::get('/get-users-by-company', function (\Illuminate\Http\Request $request) {
        // Şirket adı parametresini al
        $companyName = $request->input('company_name'); // veya request('company_name');
    
        // Şirket adına göre kullanıcıları filtrele
        $users = \App\UserAccount::where('company_name', $companyName)->get(['user_name', 'email']);
    
        return response()->json($users);
    });
    
    Route::post('/teklif/store', [TeklifController::class, 'store'])->name('teklif.store');
    Route::post('/offer/store', [OfferController::class, 'store'])->name('offer.store');
    Route::get('/offer/view/{id}', [OfferController::class, 'view'])->name('offer.view');
    Route::get('/teklif-hazirla', [OfferController::class, 'index'])->name('teklif-hazirla.browse');
    Route::put('/offer/update/{id}', [OfferController::class, 'update'])->name('offer.update');
    Route::post('/offer/set-editable/{id}', [OfferController::class, 'setEditable'])->name('offer.setEditable');
    Route::post('/offer/send/{id}', [OfferController::class, 'send'])->name('offer.send');
    Route::post('/offer/update-details/{id}', [OfferController::class, 'updateDetails'])->name('offer.updateDetails');
    
    Route::post('/project/store', [ProjectController::class, 'store'])->name('project.store');
    // Teklif iptal route'u
    Route::post('/offer/cancel/{id}', [OfferController::class, 'cancel'])->name('offer.cancel');
    Route::get('/project/view/{offerId}', [ProjectController::class, 'view'])->name('project.view');
    
    Route::post('/fatura/store', [BillsController::class, 'store'])->name('fatura.store');

    Route::get('/get-sum-for-tag', [MeasurementController::class, 'getSumForTag'])->name('getSumForTag');
    
    Voyager::routes();
    // Route::get('/ekran', ['uses' => 'Dashboards@index',   'as' => 'voyager.dashboard']);
});
