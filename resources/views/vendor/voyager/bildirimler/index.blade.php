@extends('voyager::master')

@section('page_title', 'Bildirilmiş Olaylar')

@section('page_header')
    <div class="container-fluid">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 0;">
            <h1 class="page-title" style="margin:0 !important;">
                <i class="voyager-bell"></i> Bildirilmiş Olaylar
            </h1>
            <a href="{{ route('events.create') }}" class="btn btn-primary" style="border-radius: 5px; margin-top: 15px;">
                <i class="voyager-plus"></i> Yeni Ekle
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="panel panel-bordered" style="border-radius: 8px;">
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="dataTable" class="table table-hover" style="width:100% !important; border-collapse: collapse !important;">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Cihaz & Parametre</th>
                                <th style="width: 20%;">Eşik Ayarları</th>
                                <th style="width: 25%;">İletişim</th>
                                <th class="text-center" style="width: 15%;">Durum</th>
                                <th class="text-right" style="width: 15%;">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <div style="display: flex; align-items: center;">
                                            <div style="min-width: 35px; height: 35px; background: #f0f5ff; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                                                <i class="voyager-params" style="color: #2e71f3;"></i>
                                            </div>
                                            <div>
                                                <div style="font-weight: 600; color: #333;">{{ $event->device->name ?? '-' }}</div>
                                                @php
                                                    $tags = json_decode($event->device->tags ?? '[]', true);
                                                    $tagName = $tags[$event->tag_id] ?? $event->tag_id;
                                                @endphp
                                                <small class="text-muted" style="font-size: 11px;">{{ $tagName }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="display: inline-block; background: #fff1f0; color: #f5222d; padding: 2px 8px; border-radius: 4px; font-size: 12px; margin-right: 5px;">Min: {{ $event->min_value }}</span>
                                        <span style="display: inline-block; background: #f6ffed; color: #52c41a; padding: 2px 8px; border-radius: 4px; font-size: 12px;">Max: {{ $event->max_value }}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <div style="font-size: 13px;"><i class="voyager-mail" style="font-size: 12px; color: #999;"></i> {{ $event->email }}</div>
                                        <small class="text-muted" style="font-size: 11px;">{{ $event->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        @if($event->status == 1)
                                            <span class="label label-success" style="border-radius: 12px; padding: 4px 12px;">Aktif</span>
                                        @else
                                            <span class="label label-danger" style="border-radius: 12px; padding: 4px 12px;">Pasif</span>
                                        @endif
                                    </td>
                                    <td class="text-right" style="vertical-align: middle;">
                                        <button class="btn btn-sm btn-link" style="color: #ff4d4f; padding: 0 10px;"><i class="voyager-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Kaymaları önlemek için özel CSS */
        #dataTable_wrapper .row { margin: 10px 0 !important; }
        .table > tbody > tr > td { border-top: 1px solid #f0f0f0 !important; padding: 12px 8px !important; }
        .table > Confederation > tr > th { border-bottom: 2px solid #f0f0f0 !important; background: #fafafa; }
        .dataTables_filter input { 
            border: 1px solid #d9d9d9 !important; 
            border-radius: 4px !important; 
            padding: 5px 10px !important; 
        }
    </style>
@stop

@section('javascript')
    <script>
        $(document).ready(function () {
            if ($.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable().destroy();
            }

            $('#dataTable').DataTable({
                "order": [],
                "language": { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Turkish.json" },
                "autoWidth": false, // Genişlik kaymalarını önlemek için kritik
                "responsive": true
            });
        });
    </script>
@stop