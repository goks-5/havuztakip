<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Fault;

class FaultController extends Controller
{
    public function index(Request $request)
    {
        $query = Fault::where('status', 'Onay |1|')->orderBy('created_at', 'desc');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('fault_type', 'like', "%{$search}%")
                  ->orWhere('fault_code', 'like', "%{$search}%")
                  ->orWhere('fault_comment', 'like', "%{$search}%")
                  ->orWhere('reporting_user', 'like', "%{$search}%")
                  ->orWhere('maintainer_note', 'like', "%{$search}%")
                  ->orWhereHas('staff', function ($staffQuery) use ($search) {
                      $staffQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('equipment', function ($equipQuery) use ($search) {
                      $equipQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $faults = $query->paginate(100);

        return view('vendor.voyager.arizalar.browse', compact('faults'));
    }

    public function store(Request $request)
    {
        Fault::create([
            'equipment_id' => $request->equipment_id,
            'fault_type' => $request->fault_type,
            'fault_code' => $request->fault_code,
            'fault_comment' => $request->fault_comment,
            'reporting_user' => $request->reporting_user,
            'status' => 'Yeni',
            'company_id' => auth()->user()->company_id, 
        ]);

        return redirect()->back()->with('success', 'Arıza başarıyla eklendi.');
    }

    public function closeSelected(Request $request)
    {
        $ids = explode(',', $request->input('selected_ids'));

        Fault::whereIn('id', $ids)->update([
            'status' => 'Bitti |1|'
        ]);

        return redirect()->back()->with([
            'message' => 'Seçilen arızalar başarıyla kapatıldı.',
            'alert-type' => 'success'
        ]);
    }

    // Eğer diğer işlemleri de kullanıyorsan actions() methodunu da burada bulundurabilirsin.
    public function actions(Request $request)
    {
        if ($request->action === 'close_selected') {
            $ids = explode(',', $request->input('selected_ids'));
            Fault::whereIn('id', $ids)->update(['status' => 'Bitti |1|']);

            return back()->with([
                'message' => 'Arızalar kapatıldı.',
                'alert-type' => 'success'
            ]);
        }

        return back();
    }

    public function show(Request $request, $id)
    {
        $fault = Fault::findOrFail($id);
        
        // Aşağıdaki satır view dosyanızın bulunduğu yolu yansıtmalı
        return view('vendor.voyager.yeni-gelen-is-emirleri.show', compact('fault'));
    }

    public function edit(Request $request, $id)
    {
        $fault = Fault::findOrFail($id);

        // Durum eşlemesi: kullanıcıya gösterilecek etiket => veritabanına kaydedilecek değer
        $mappedStatuses = [
            'Yeni'              => 'Yeni',
            'İşleme alındı'     => 'Bekliyor |0|',
            'Başladı'           => 'Bakıma Başlandı |0|',
            'Firma'             => 'Firma Yönlendirildi |2|',
            'Malzeme'           => 'Malzeme Bekliyor |2|',
            'Onay'              => 'Onay |1|',
            'Biten'             => 'Bitti |1|',
        ];

        // Arıza tipi seçenekleri
        $fault_types = ['Arıza', 'Bakım', 'Planlı Duruş', 'Montaj', 'ISG', 'Diğer'];

        // Arıza kodu seçenekleri (key: kod, value: açıklama)
        $fault_codes = [
            '100' => 'Mekanik',
            '200' => 'Elektrik',
            '300' => 'Bakım',
            '400' => 'Tesisat',
            '500' => 'Kaynak',
            '600' => 'İnşaat',
            '700' => 'Genel',
            '800' => 'Montaj'
        ];

        // İlgili ekipmanlar 
        $equipments = \App\Equipment::all();

        // Bakımcı listesi (tüm staff kayıtları)
        $staffs = \App\Staff::all();

        return view('vendor.voyager.yeni-gelen-is-emirleri.edit', compact(
            'fault',
            'mappedStatuses',
            'fault_types',
            'fault_codes',
            'equipments',
            'staffs'
        ));
    }

    public function tamamlananlarBrowse(Request $request)
    {
        // 1) Giriş yapan kullanıcının company_id'si
        $companyId = auth()->user()->company_id;

        // 2) "Bitti |1|" statüsündeki ve kendi şirketinize ait kayıtlar
        $query = Fault::where('status', 'Bitti |1|')
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc');

        // 3) Arama filtresi
        if ($search = $request->input('search')) {
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

        // 4) Tarih aralığı filtresi (mevcut kodunuz neyse koruyun)
        if ($range = $request->input('date_range')) {
            $dates = explode(' to ', $range);
            if (count($dates) === 2) {
                $start = trim($dates[0]);
                $end   = trim($dates[1]);
                $query->whereBetween('created_at', [$start, $end]);
            }
        }

        // 5) Sayfalama
        $faults = $query
            ->paginate(100)
            ->appends($request->only('search', 'date_range'));

        return view('vendor.voyager.tamamlananlar.browse', compact('faults'));
    }

    public function tumIsEmirleriBrowse(Request $request)
    {
        $companyId = auth()->user()->company_id;

        // 1) Query’i kuruyoruz
        $query = Fault::with(['staff','equipment'])
            ->where('company_id', $companyId)
            ->when($request->filled('status'),
                  fn($q)=> $q->where('status', $request->status))
            ->when($request->filled('durum'),
                  fn($q)=> $q->where('status','like','%'.$request->durum.'%'))
            ->when($request->filled('ekipman'),
                  fn($q)=> $q->whereHas('equipment',
                      fn($eq)=> $eq->where('name','like','%'.$request->ekipman.'%')
                  ))
            ->when($request->filled('fault_type'),
                  fn($q)=> $q->where('fault_type','like','%'.$request->fault_type.'%'))
            ->when($request->filled('fault_code'),
                  fn($q)=> $q->where('fault_code','like','%'.$request->fault_code.'%'))
            ->when($request->filled('fault_comment'),
                  fn($q)=> $q->where('fault_comment','like','%'.$request->fault_comment.'%'))
            ->when($request->filled('reporting_user'),
                  fn($q)=> $q->where('reporting_user','like','%'.$request->reporting_user.'%'))
            ->when($request->filled('created_at'),
                  fn($q)=> $q->whereDate('created_at',$request->created_at))
            ->when($request->filled('finish_at'),
                  fn($q)=> $q->whereDate('finish_at',$request->finish_at))
            ->when($request->filled('staff'),
                  fn($q)=> $q->whereHas('staff',
                      fn($st)=> $st->where('name','like','%'.$request->staff.'%')
                  ))
            ->when($request->filled('maintainer_note'),
                  fn($q)=> $q->where('maintainer_note','like','%'.$request->maintainer_note.'%'))
            ->when($request->filled('date_range'), function($q) use($request) {
                $parts = explode(' to ', $request->date_range);
                if(count($parts)===2) {
                  $q->whereBetween('created_at', [
                     trim($parts[0]), trim($parts[1])
                  ]);
                }
            })
            ->orderBy('created_at','desc');

        // 2) Eğer export=1 geldiyse CSV döndür
        if ($request->filled('export')) {
            $rows = $query->get();
            $filename = 'is-emirleri_'.now()->format('Ymd_His').'.csv';
            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];
            $columns = [
              'Durum','Ekipman','Arıza Tipi','Arıza Kodu',
              'Arıza Açıklaması','Bildiren Personel',
              'Oluşturma','Tamamlanma','Bakımcı','Not'
            ];
            $callback = function() use($rows,$columns){
                $out = fopen('php://output','w');
                fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($out, $columns);
                foreach($rows as $f){
                    fputcsv($out, [
                      $f->status,
                      optional($f->equipment)->name,
                      $f->fault_type,
                      $f->fault_code,
                      $f->fault_comment,
                      $f->reporting_user,
                      $f->created_at,
                      $f->finish_at,
                      optional($f->staff)->name,
                      $f->maintainer_note,
                    ]);
                }
                fclose($out);
            };
            return response()->stream($callback,200,$headers);
        }

        // 3) Aksi halde pagination’a devam
        $faults = $query->paginate(10)->appends($request->all());

        $statusList = [
          ''                        => 'Tümü',
          'Yeni'                    => 'Yeni',
          'Bekliyor |0|'            => 'Bekliyor',
          'Bakıma Başlandı |0|'     => 'Bakıma Başlandı',
          'Firma Yönlendirildi |2|'=> 'Firmaya Yönlendirildi',
          'Malzeme Bekliyor |2|'    => 'Malzeme Bekleniyor',
          'Onay |1|'                => 'Onay',
          'Bitti |1|'               => 'Bitti',
        ];

        return view('vendor.voyager.tum-is-emirleri.browse', compact('faults','statusList'));
    }

    public function export(Request $request)
    {
        $companyId = auth()->user()->company_id;

        // Aynı filtreleme mantığı:
        $query = Fault::with(['staff','equipment'])
            ->where('company_id',$companyId)
            ->when($request->filled('status'),
                  fn($q)=> $q->where('status',$request->status)
            )
            ->when($request->filled('durum'),
                  fn($q)=> $q->where('status','like','%'.$request->durum.'%')
            )
            ->when($request->filled('ekipman'),
                  fn($q)=> $q->whereHas('equipment',
                      fn($eq)=> $eq->where('name','like','%'.$request->ekipman.'%')
                  )
            )
            // … diğer sütun filtreleri tıpkı yukarıdaki gibi eklenmeli …
            ->when($request->filled('date_range'), function($q) use($request) {
                $parts = explode(' to ', $request->date_range);
                if(count($parts)===2) {
                    $q->whereBetween('created_at',[trim($parts[0]),trim($parts[1])]);
                }
            })
            ->orderBy('created_at','desc');

        $rows = $query->get();

        // CSV üret
        $filename = 'is-emirleri_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        $cols = ['Durum','Ekipman','Arıza Tipi','Arıza Kodu','Arıza Açıklaması','Bildiren Personel','Oluşturma','Tamamlanma','Bakımcı','Not'];

        $callback = function() use($rows,$cols) {
            $out = fopen('php://output','w');
            // UTF-8 BOM
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, $cols);
            foreach($rows as $f){
                fputcsv($out, [
                    $f->status,
                    optional($f->equipment)->name,
                    $f->fault_type,
                    $f->fault_code,
                    $f->fault_comment,
                    $f->reporting_user,
                    $f->created_at,
                    $f->finish_at,
                    optional($f->staff)->name,
                    $f->maintainer_note,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function update(Request $request, $id)
    {
        // ID'ye göre ilgili kaydı bulun
        $fault = Fault::findOrFail($id);

        // Formdan gelen verileri kayda uygula (örnek)
        $fault->status = $request->input('status'); 
        $fault->fault_type = $request->input('fault_type');
        $fault->fault_code = $request->input('fault_code');
        $fault->fault_comment = $request->input('fault_comment');
        $fault->reporting_user = $request->input('reporting_user');
        $fault->finish_at = $request->input('finish_at');
        $fault->maintainer_note = $request->input('maintainer_note');
        // vs. diğer alanlar...

        // Değişiklikleri veritabanına kaydet
        $fault->save();

        // Başarılı güncelleme sonrası bir sayfaya yönlendirin
        // Örneğin "show" sayfasına geri dönmek:
        return redirect()->route('yeni-gelen-is-emirleri.show', $fault->id)
                        ->with('success', 'Kayıt başarıyla güncellendi!');
    }

    public function destroy(Request $request, $id)
    {
        $fault = Fault::findOrFail($id);
        $fault->delete();

        return redirect()->route('yeni-gelen-is-emirleri.browse')
                        ->with('success', 'Kayıt başarıyla silindi!');
    }

    public function browse(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Fault::where('status', 'Yeni')
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc');

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('fault_type', 'like', "%{$search}%")
                ->orWhere('fault_code', 'like', "%{$search}%")
                ->orWhere('fault_comment', 'like', "%{$search}%")
                ->orWhere('reporting_user', 'like', "%{$search}%")
                ->orWhere('maintainer_note', 'like', "%{$search}%")
                ->orWhereHas('staff', fn($st)=> $st->where('name', 'like', "%{$search}%"))
                ->orWhereHas('equipment', fn($eq)=> $eq->where('name', 'like', "%{$search}%"));
            });
        }

        $faults = $query->paginate(100)->appends($request->only('search'));
        $staffs = \App\Staff::where('company_id', $companyId)->get();

        return view('vendor.voyager.yeni-gelen-is-emirleri.browse', compact('faults', 'staffs'));
    }
    
    public function accept(Request $request, $id)
    {
        $fault = Fault::findOrFail($id);

        // 1. Formdan gelen staff_id değerini maintainer_id alanına ata
        $fault->maintainer_id = $request->input('staff_id');

        // 2. Durumu güncelle (örnek: Bekliyor |0|)
        $fault->status = 'Bekliyor |0|';

        // 3. Veritabanına kaydet
        $fault->save();

        // 4. Başarılı işlem sonrası mevcut sayfada kal
        return redirect()->back()->with('success', 'Arıza kabul edildi ve bakımcı atandı.');
    }

    public function pdf($id)
    {
        $fault = Fault::findOrFail($id);
        $url = route('yeni-gelen-is-emirleri.pdfView', $fault->id);
        $filename = 'ariza_' . $fault->id . '.pdf';
        $pdfPath = storage_path('app/public/' . $filename);

        // wkhtmltopdf komutu
        $command = "wkhtmltopdf --enable-local-file-access "
                . escapeshellarg($url) . " " . escapeshellarg($pdfPath);

        // Komutu çalıştır
        exec($command, $output, $return_var);

        // Dönüş değeri 0 ise başarılı demektir
        if ($return_var === 0) {
            // PDF’i sunucudaki dosyadan okuyup kullanıcıya gönder
            return response()->file($pdfPath, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$filename.'"'
            ]);
        } else {
            // Hata durumunda debug bilgisi göster
            dd('PDF oluşturulamadı!', $return_var, $output, $command);
        }
    }

    public function pdfView($id)
    {
        $fault = Fault::findOrFail($id);

        // pdf.blade.php dosyanızı (resources/views/pdf.blade.php veya vendor/voyager/...) render edin
        return view('vendor.voyager.yeni-gelen-is-emirleri.pdf', compact('fault'));
    }

    public function process(Request $request, $id)
    {
        $fault = Fault::findOrFail($id);

        // Formdan gelen verileri kaydet
        $fault->status = $request->input('status');
        $fault->maintainer_note = $request->input('comment'); // Örnek: comment'i maintainer_note alanına yazmak
        $fault->save();

        // Başarılı işlem sonrası geri yönlendir
        return redirect()->back()->with('success', 'İşlem kaydedildi!');
    }

}
