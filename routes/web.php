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
use App\Http\Controllers\EventController;
use App\Fault;
use App\Http\Controllers\FaultController;
use App\Http\Controllers\VerilerController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
    })->name('firma-tablosu.browse')->middleware('role:34');
    
    Route::post('/firma-tablosu/store', [CompanyController::class, 'store'])
    ->name('firma-tablosu.store')->middleware('role:34');
    Route::delete('/firma-tablosu/destroy/{id}', [CompanyController::class, 'destroy'])
    ->name('firma-tablosu.destroy')->middleware('role:34');
    Route::put('/firma-tablosu/update/{id}', [CompanyController::class, 'update'])
    ->name('firma-tablosu.update')->middleware('role:34');
    
    // Kullanıcı Tablosu Rotaları (Değiştirildi)
     Route::get('/kullanici-tablosu', [UserController::class, 'index'])
    ->name('kullanici-tablosu.index')->middleware('role:34');
    Route::post('/kullanici-tablosu/store', [UserController::class, 'store'])
    ->name('kullanici-tablosu.store')->middleware('role:34');
    Route::put('/kullanici-tablosu/update/{id}', [UserController::class, 'update'])
    ->name('kullanici-tablosu.update')->middleware('role:34');
    Route::delete('/kullanici-tablosu/destroy/{id}', [UserController::class, 'destroy'])
    ->name('kullanici-tablosu.destroy')->middleware('role:34');

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
    Route::get('/teklif-hazirla', [OfferController::class, 'index'])
    ->name('teklif-hazirla.browse')
    ->middleware('role:34');
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

    Route::post('/faults/store', [FaultController::class, 'store'])->name('fault.store');

    Route::post('/is-emri-bildir', [FaultController::class, 'store'])->name('fault.store');

    Route::get('yeni-gelen-is-emirleri/{id}', [FaultController::class, 'show'])
    ->name('yeni-gelen-is-emirleri.show');

    Route::get('yeni-gelen-is-emirleri/{id}/edit', [FaultController::class, 'edit'])
    ->name('yeni-gelen-is-emirleri.edit');

    Route::put('yeni-gelen-is-emirleri/{id}', [FaultController::class, 'update'])
    ->name('yeni-gelen-is-emirleri.update');

    Route::delete('yeni-gelen-is-emirleri/{id}', [FaultController::class, 'destroy'])
    ->name('yeni-gelen-is-emirleri.destroy');

    Route::post('yeni-gelen-is-emirleri/{id}/accept', [FaultController::class, 'accept'])
     ->name('yeni-gelen-is-emirleri.accept');

     Route::get('yeni-gelen-is-emirleri', [FaultController::class, 'browse'])
    ->name('yeni-gelen-is-emirleri.browse');

    Route::post('yeni-gelen-is-emirleri/{id}/process', [FaultController::class, 'process'])
     ->name('yeni-gelen-is-emirleri.process');

    Route::get('yeni-gelen-is-emirleri/{id}/pdf', [\App\Http\Controllers\FaultController::class, 'pdf'])
    ->name('yeni-gelen-is-emirleri.pdf');

    Route::get('yeni-gelen-is-emirleri/{id}/pdfView', [\App\Http\Controllers\FaultController::class, 'pdfView'])
     ->name('yeni-gelen-is-emirleri.pdfView');

    Route::get('/islemdekiler', function () {
        // 1) Giriş yapan kullanıcının company_id'sini alın
        $companyId = Auth::user()->company_id;

        // 2) "Bekliyor |0|" ve "Bakıma Başlandı |0|" statülerindeki ve sadece kendi company_id'nize ait kayıtlar
        $query = Fault::whereIn('status', ['Bekliyor |0|', 'Bakıma Başlandı |0|'])
                    ->where('company_id', $companyId)
                    ->orderBy('created_at', 'desc');

        // 3) Arama filtresi varsa ekleyin
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('fault_type', 'like', "%{$search}%")
                ->orWhere('fault_code', 'like', "%{$search}%")
                ->orWhere('fault_comment', 'like', "%{$search}%")
                ->orWhere('reporting_user', 'like', "%{$search}%")
                ->orWhere('maintainer_note', 'like', "%{$search}%")
                ->orWhereHas('staff', function($staffQ) use ($search) {
                    $staffQ->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('equipment', function($eqQ) use ($search) {
                    $eqQ->where('name', 'like', "%{$search}%");
                });
            });
        }

        // 4) Sayfalama
        $faults = $query->paginate(100)->appends(request()->only('search'));

        return view('vendor.voyager.islemdekiler.browse', compact('faults'));
    })->name('islemdekiler.browse');

    Route::get('/beklemeye-alinanlar', function () {
        // 1) Giriş yapanın şirketi
        $companyId = Auth::user()->company_id;

        // 2) İstenen statüler + kendi şirketinize ait kayıtlar
        $query = Fault::whereIn('status', [
                        'Firma Yönlendirildi |2|',
                        'Malzeme Bekliyor |2|'
                    ])
                    ->where('company_id', $companyId)
                    ->orderBy('created_at', 'desc');

        // 3) Arama filtrelemesi
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('fault_type', 'like', "%{$search}%")
                ->orWhere('fault_code', 'like', "%{$search}%")
                ->orWhere('fault_comment', 'like', "%{$search}%")
                ->orWhere('reporting_user', 'like', "%{$search}%")
                ->orWhere('maintainer_note', 'like', "%{$search}%")
                ->orWhereHas('staff', function($st) use ($search) {
                    $st->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('equipment', function($eq) use ($search) {
                    $eq->where('name', 'like', "%{$search}%");
                });
            });
        }

        // 4) Sayfalama (arama terimini linklere ekleyin)
        $faults = $query->paginate(100)->appends(request()->only('search'));

        return view('vendor.voyager.beklemeye-alinanlar.browse', compact('faults'));
    })->name('beklemeye-alinanlar.browse');

    Route::get('/onay-bekleyenler', function () {
        // 1) Giriş yapan kullanıcının company_id'sini alalım
        $companyId = Auth::user()->company_id;

        // 2) Sadece status = 'Onay |1|' ve kendi şirketine ait kayıtlar
        $query = Fault::where('status', 'Onay |1|')
                    ->where('company_id', $companyId)
                    ->orderBy('created_at', 'desc');

        // 3) Arama parametresi varsa uygulayalım
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('fault_type',    'like', "%{$search}%")
                ->orWhere('fault_code',    'like', "%{$search}%")
                ->orWhere('fault_comment', 'like', "%{$search}%")
                ->orWhere('reporting_user','like', "%{$search}%")
                ->orWhere('maintainer_note','like', "%{$search}%")
                ->orWhereHas('staff', function($st) use ($search) {
                    $st->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('equipment', function($eq) use ($search) {
                    $eq->where('name', 'like', "%{$search}%");
                });
            });
        }

        // 4) Sayfalama ve arama terimini linklere ekleme
        $faults = $query->paginate(100)
                        ->appends(request()->only('search'));

        return view('vendor.voyager.onay-bekleyenler.browse', compact('faults'));
    })->name('onay-bekleyenler.browse');

    Route::get('/arizalar', [FaultController::class, 'index'])->name('arizalar.browse');
    Route::post('/arizalar/kapat', [FaultController::class, 'closeSelected'])->name('arizalar.kapat');
    Route::post('/ajax/faults_actions', [FaultController::class, 'actions'])->name('faultsActions');

    Route::get('/tamamlananlar', [FaultController::class, 'tamamlananlarBrowse'])->name('tamamlananlar.browse');

    Route::get('/tum-is-emirleri', [FaultController::class, 'tumIsEmirleriBrowse'])
    ->name('tum-is-emirleri.browse');

    Route::get('/tum-is-emirleri/export', [FaultController::class,'export'])
     ->name('tum-is-emirleri.export');

    Route::get('/projeler', function () {
    $offers = DB::table('offer')->where('is_editable', 2)->get();
    return view('vendor.voyager.projeler.browse', compact('offers'));
    })->name('projeler.browse')->middleware('role:34');

    // Listeleme + arama
    Route::get('veriler', [VerilerController::class, 'index'])
    ->name('voyager.veriler.browse');

    // Tekil gösterim
    Route::get('veriler/{id}', [VerilerController::class, 'show'])
    ->name('veriler.show');

    Route::get('/mail-info-circle-now', function () {
        $companyId = Auth::user()->company_id; // oturumdaki şirket
        $devices = DB::table('devices')
            ->where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->get(['id','mac','name','tags','last_data','last_at','tags_last_change']);

        $timeout1 = setting('device.ofline') * 60;
        $timeout2 = setting('device.oflinesayac');
        $timeout3 = setting('device.tag') * 60;

        $summary = [
            'ofline'          => 0,
            'deviceCount'     => $devices->count(),
            'pointCount'      => 0,
            'oflineCount'     => 0,
            'oflineDevices'   => [],
            'changeTagsCount' => 0,
            'changeTags'      => [],
        ];

        foreach ($devices as $device) {
            $tags = [];
            if (!is_null($device->tags)) {
                $tags = array_filter(json_decode($device->tags, true), function ($k) {
                    return $k < '1000';
                }, ARRAY_FILTER_USE_KEY);
            }

            $summary['pointCount'] += count($tags);

            $timeout = ($device->mac === '00:00:00:00:00:01') ? $timeout2 : $timeout1;

            if (strtotime($device->last_at) + $timeout < time() && $device->mac !== '00:00:00:00:00:02') {
                $summary['oflineCount']++;
                $summary['oflineDevices'][] = [
                    'name'    => $device->name,
                    'mac'     => $device->mac,
                    'last_at' => $device->last_at,
                ];
                $summary['ofline'] = 1;
            }

            // 1000 ve üzeri: değişim izlenen etiketler
            $changeTags = [];
            if (!is_null($device->tags)) {
                $changeTags = array_filter(json_decode($device->tags, true), function ($k) {
                    return $k >= '1000';
                }, ARRAY_FILTER_USE_KEY);
            }
            $changeAt = json_decode($device->tags_last_change, true) ?: [];
            $changeTags = array_replace($changeTags, $changeAt);

            foreach ($changeTags as $tagkey => $value) {
                if (strtotime($value) + $timeout3 < time()) {
                    $tagName = $tags[$tagkey - 1000] ?? ('Tag#' . ($tagkey - 1000));
                    $summary['changeTags'][] = [
                        'name'        => $device->name,
                        'tag'         => $tagName,
                        'last_change' => $value,
                    ];
                    $summary['changeTagsCount']++;
                    $summary['ofline'] = 1;
                }
            }
        }

        // Mailable oluşturmadan direkt view ile gönder
        Mail::send('emails.info_circle', ['summary' => $summary], function ($m) {
            $m->to('gookceturun@gmail.com')
            ->subject('Enerji Yönetim - Durum Özeti');
        });

        return 'Durum özeti maili gönderildi.';
    })->middleware('auth');
    
    // Liste Sayfası
    Route::get('bildirilmis-olaylar', [EventController::class, 'index'])->name('events.index');
    
    // Yeni Ekleme Sayfası ve Kaydetme
    Route::get('olay-ekle', [EventController::class, 'create'])->name('events.create');
    Route::post('olay-ekle', [EventController::class, 'store'])->name('events.store');
    
    // Düzenleme Sayfası ve Güncelleme
    Route::get('olay-duzenle/{id}', [EventController::class, 'edit'])->name('events.edit');
    Route::put('olay-guncelle/{id}', [EventController::class, 'update'])->name('events.update');
    
    // Silme İşlemi
    Route::delete('olay-sil/{id}', [EventController::class, 'destroy'])->name('events.destroy');
    
    // AJAX Tag Getirme (Tek ve Doğru Rota)
    Route::get('get-tags/{deviceId}', [EventController::class, 'getTags'])->name('events.get-tags');
    
    Voyager::routes();
    // Route::get('/ekran', ['uses' => 'Dashboards@index',   'as' => 'voyager.dashboard']);
});

