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
use App\Fault;
use App\Http\Controllers\FaultController;

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
    
    Route::get('/is-emri-bildir', function () {
        return view('vendor.voyager.is-emri-bildir.browse');
    })->name('is-emri-bildir.browse');
    
    Route::get('/yeni-gelen-is-emirleri', function () {
        // Sadece "Yeni" statüsündeki kayıtları getirelim:
        $query = Fault::where('status', 'Yeni')->orderBy('created_at', 'desc');

        // Arama yapılıyorsa, ilgili sütunlarda arama yap:
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('fault_type', 'like', "%{$search}%")
                ->orWhere('fault_code', 'like', "%{$search}%")
                ->orWhere('fault_comment', 'like', "%{$search}%")
                ->orWhere('reporting_user', 'like', "%{$search}%")
                ->orWhere('maintainer_note', 'like', "%{$search}%");
                
                // İlişkili tablolar üzerinden de arama yapabilirsiniz:
                $q->orWhereHas('staff', function($staffQuery) use ($search) {
                    $staffQuery->where('name', 'like', "%{$search}%");
                });
                $q->orWhereHas('equipment', function($equipQuery) use ($search) {
                    $equipQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        // 100 kayıtla sayfalama
        $faults = $query->paginate(100);

        return view('vendor.voyager.yeni-gelen-is-emirleri.browse', compact('faults'));
    })->name('yeni-gelen-is-emirleri.browse');

    Route::get('/islemdekiler', function () {
        // "Bekliyor |0|" ve "Bakıma Başlandı |0|" statülerine sahip kayıtları getiriyoruz.
        $query = Fault::whereIn('status', ['Bekliyor |0|', 'Bakıma Başlandı |0|'])
                    ->orderBy('created_at', 'desc');

        // Arama parametresi varsa, ilgili sütunlarda arama yapıyoruz.
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('fault_type', 'like', "%{$search}%")
                ->orWhere('fault_code', 'like', "%{$search}%")
                ->orWhere('fault_comment', 'like', "%{$search}%")
                ->orWhere('reporting_user', 'like', "%{$search}%")
                ->orWhere('maintainer_note', 'like', "%{$search}%");

                // İlişkili tablolarda da arama (staff, equipment)
                $q->orWhereHas('staff', function($staffQuery) use ($search) {
                    $staffQuery->where('name', 'like', "%{$search}%");
                });
                $q->orWhereHas('equipment', function($equipQuery) use ($search) {
                    $equipQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        // 100 kayıtla sayfalama
        $faults = $query->paginate(100);

        return view('vendor.voyager.islemdekiler.browse', compact('faults'));
    })->name('islemdekiler.browse');

    Route::get('/beklemeye-alinanlar', function () {
        // Sadece "Firma Yönlendirildi |2|" ve "Malzeme Bekliyor |2|" statüsündeki kayıtları getiriyoruz.
        $query = Fault::whereIn('status', ['Firma Yönlendirildi |2|', 'Malzeme Bekliyor |2|'])
                    ->orderBy('created_at', 'desc');

        // Arama parametresi varsa, faults tablosunun ilgili sütunlarında ve ilişkili tablolarda arama yapıyoruz.
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('fault_type', 'like', "%{$search}%")
                ->orWhere('fault_code', 'like', "%{$search}%")
                ->orWhere('fault_comment', 'like', "%{$search}%")
                ->orWhere('reporting_user', 'like', "%{$search}%")
                ->orWhere('maintainer_note', 'like', "%{$search}%");

                // İlişkili tablolar üzerinden arama (staff, equipment)
                $q->orWhereHas('staff', function($staffQuery) use ($search) {
                    $staffQuery->where('name', 'like', "%{$search}%");
                });
                $q->orWhereHas('equipment', function($equipQuery) use ($search) {
                    $equipQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        // 100 kayıtla sayfalama
        $faults = $query->paginate(100);

        return view('vendor.voyager.beklemeye-alinanlar.browse', compact('faults'));
    })->name('beklemeye-alinanlar.browse');

    Route::get('/onay-bekleyenler', function () {
        // Sadece status "Onay |1|" olan kayıtları getiriyoruz.
        $query = Fault::where('status', 'Onay |1|')->orderBy('created_at', 'desc');

        // Arama parametresi varsa, faults tablosunun ilgili sütunlarında arama yapıyoruz.
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('fault_type', 'like', "%{$search}%")
                ->orWhere('fault_code', 'like', "%{$search}%")
                ->orWhere('fault_comment', 'like', "%{$search}%")
                ->orWhere('reporting_user', 'like', "%{$search}%")
                ->orWhere('maintainer_note', 'like', "%{$search}%");

                // İlişkili tablolar üzerinden de arama yapıyoruz:
                $q->orWhereHas('staff', function($staffQuery) use ($search) {
                    $staffQuery->where('name', 'like', "%{$search}%");
                });
                $q->orWhereHas('equipment', function($equipQuery) use ($search) {
                    $equipQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        // 100 kayıtla sayfalama
        $faults = $query->paginate(100);

        return view('vendor.voyager.onay-bekleyenler.browse', compact('faults'));
    })->name('onay-bekleyenler.browse');

    Route::get('/arizalar', [FaultController::class, 'index'])->name('arizalar.browse');
    Route::post('/arizalar/kapat', [FaultController::class, 'closeSelected'])->name('arizalar.kapat');
    Route::post('/ajax/faults_actions', [FaultController::class, 'actions'])->name('faultsActions');

    Route::get('/tamamlananlar', function () {
        // Sadece status "Bitti |1|" olan kayıtları oluşturulma tarihine göre sıralayalım.
        $query = Fault::where('status', 'Bitti |1|')->orderBy('created_at', 'desc');

        // Arama parametresi varsa, faults tablosunun ilgili sütunlarında ve ilişkili tablolarda arama yapalım.
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('fault_type', 'like', "%{$search}%")
                ->orWhere('fault_code', 'like', "%{$search}%")
                ->orWhere('fault_comment', 'like', "%{$search}%")
                ->orWhere('reporting_user', 'like', "%{$search}%")
                ->orWhere('maintainer_note', 'like', "%{$search}%");

                // İlişkili tablolar üzerinden arama (staff, equipment)
                $q->orWhereHas('staff', function($staffQuery) use ($search) {
                    $staffQuery->where('name', 'like', "%{$search}%");
                });
                $q->orWhereHas('equipment', function($equipQuery) use ($search) {
                    $equipQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        // 100 kayıtla sayfalama
        $faults = $query->paginate(100);

        return view('vendor.voyager.tamamlananlar.browse', compact('faults'));
    })->name('tamamlananlar.browse');

    Route::get('/tum-is-emirleri', function () {
        // Filtrelenecek durumlar
        $statusList = [
            '' => 'Tümü',
            'Yeni' => 'Yeni',
            'Bekliyor |0|' => 'Bekliyor',
            'Bakıma Başlandı |0| ' => 'Bakıma Başlandı',
            'Firma Yönlendirildi |2|' => 'Firmaya Yönlendirildi',
            'Malzeme Bekliyor |2|' => 'Malzeme Bekleniyor',
            'Onay |1|' => 'Onay',
            'Bitti |1|' => 'Bitti',
        ];

        // Ana sorgu
        $query = Fault::orderBy('created_at', 'desc');

        // 1) Status filtre
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        // 2) Genel arama
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                // faults tablosu sütunlarında arama
                $q->where('status', 'like', "%{$search}%")
                ->orWhere('fault_type', 'like', "%{$search}%")
                ->orWhere('fault_code', 'like', "%{$search}%")
                ->orWhere('fault_comment', 'like', "%{$search}%")
                ->orWhere('reporting_user', 'like', "%{$search}%")
                ->orWhere('maintainer_note', 'like', "%{$search}%");

                // staff tablosunda da arama (ilişki tanımlı ise)
                $q->orWhereHas('staff', function($staffQuery) use ($search) {
                    $staffQuery->where('name', 'like', "%{$search}%");
                });

                // equipment tablosunda da arama (ilişki tanımlı ise)
                $q->orWhereHas('equipment', function($equipQuery) use ($search) {
                    $equipQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        // 100 kayıtla sayfalama
        $faults = $query->paginate(100);

        return view('vendor.voyager.tum-is-emirleri.browse', compact('faults', 'statusList'));
    })->name('tum-is-emirleri.browse');

    Voyager::routes();
    // Route::get('/ekran', ['uses' => 'Dashboards@index',   'as' => 'voyager.dashboard']);
});
