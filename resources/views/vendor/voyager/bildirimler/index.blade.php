@extends('voyager::master')

@section('page_title', 'Bildirilmiş Olaylar')

@section('page_header')
    <div class="container-fluid">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0;">
            <h1 class="page-title" style="margin:0 !important; font-weight: 600;">
                <i class="voyager-bell"></i> Bildirilmiş Olaylar
            </h1>
            <a href="{{ route('events.create') }}" class="btn btn-success" style="border-radius: 4px; font-weight: 600;">
                <i class="voyager-plus"></i> Yeni Ekle
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid" style="padding-top: 0;">
        @include('voyager::alerts')
        <div class="panel panel-bordered" style="border-radius: 8px; border:none; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div class="panel-body" style="padding: 15px;">
                <div class="table-responsive">
                    <table id="dataTable" class="table table-hover" style="width:100% !important;">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Cihaz</th>
                                <th style="width: 25%;">Etiket (Parametre)</th>
                                <th style="width: 15%;">Eşik Değerleri</th>
                                <th style="width: 25%;">İletişim Bilgisi</th>
                                <th class="text-right" style="width: 15%;">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr>
                                    <td style="vertical-align: middle; font-weight: 600; color: #444;">
                                        {{ $event->device->name ?? '-' }}
                                    </td>
                                    <td style="vertical-align: middle;">
                                        @php
                                            $tags = json_decode($event->device->tags ?? '[]', true);
                                            $tagName = $tags[$event->tag_id] ?? $event->tag_id;
                                        @endphp
                                        <span style="color: #3498db; font-weight: 500;">{{ $tagName }}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <div class="threshold-box min">
                                            <span class="t-label">MİN</span>
                                            <span class="t-value">{{ (float)$event->min_value }}</span>
                                        </div>
                                        <div class="threshold-box max" style="margin-top: 3px;">
                                            <span class="t-label">MAX</span>
                                            <span class="t-value">{{ (float)$event->max_value }}</span>
                                        </div>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <div style="font-size: 13px; color: #555;"><i class="voyager-mail" style="color:#ccc; font-size: 11px;"></i> {{ $event->email }}</div>
                                        <small class="text-muted" style="font-size: 10px;">{{ $event->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td class="text-right" style="vertical-align: middle;">
                                        <a href="{{ route('events.edit', $event->id) }}" class="btn btn-sm btn-primary action-btn" title="Düzenle">
                                            <i class="voyager-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-btn action-btn" data-id="{{ $event->id }}" title="Sil">
                                            <i class="voyager-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-danger" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Kapat"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> Emin misiniz?</h4>
                </div>
                <div class="modal-body text-center" style="padding: 30px;">
                    <p style="font-size: 16px; color: #333;">Bu bildirim kuralını silmek üzeresiniz.</p>
                    <p class="text-muted">Bu işlem sonucunda cihaz bildirimleri durdurulacaktır.</p>
                </div>
                <div class="modal-footer">
                    <form id="delete_form" method="POST">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-default" data-dismiss="modal">Vazgeç</button>
                        <button type="submit" class="btn btn-danger">Kuralı Sil</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Sütunlar arası denge ve eşik tasarımı */
        .threshold-box {
            display: flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            background: #fff;
            border: 1px solid #eee;
            width: 90px; /* Sabit genişlik sütun kaymasını engeller */
        }
        .threshold-box.min { border-left: 3px solid #e74c3c; color: #e74c3c; }
        .threshold-box.max { border-left: 3px solid #2ecc71; color: #2ecc71; }
        
        .t-label { font-weight: 700; font-size: 9px; margin-right: 5px; color: #aaa; }
        .t-value { margin-left: auto; font-weight: 700; }

        /* Butonlar */
        .action-btn { border-radius: 4px !important; padding: 5px 10px !important; margin-left: 2px; }
        
        /* Tablo ve DataTables İnce Ayar */
        .table thead th { background: #fafafa; color: #999; font-size: 11px; text-transform: uppercase; border-bottom: 2px solid #eee !important; }
        .table tbody td { border-top: 1px solid #f9f9f9 !important; padding: 12px 8px !important; }
        
        .dataTables_filter input { border: 1px solid #ddd !important; border-radius: 20px !important; padding: 5px 15px !important; outline: none !important; margin-bottom: 10px; }
    </style>
@stop

@section('javascript')
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                "order": [],
                "language": { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Turkish.json" },
                "autoWidth": false,
                "responsive": true,
                "columnDefs": [
                    { "targets": [4], "orderable": false } // İşlemler sütununu sıralamaya kapat
                ]
            });

            $('.delete-btn').click(function() {
                let id = $(this).data('id');
                let url = "{{ route('events.destroy', ':id') }}".replace(':id', id);
                $('#delete_form').attr('action', url);
                $('#delete_modal').modal('show');
            });
        });
    </script>
@stop