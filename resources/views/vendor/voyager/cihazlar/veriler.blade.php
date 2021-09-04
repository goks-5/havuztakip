@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing') . ' Cihaz Verileri')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-bar-chart"></i> Cihaz Verileri
        </h1>

        @include('voyager::multilingual.language-selector')
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="form-group row" style="margin-left: 10px;margin-top: 10px;">
                        <form method="POST" action="{{ route('cihazverilerajax') }}">
                            @csrf
                            <input type="hidden" id="starttime" name="starttime" />
                            <input type="hidden" id="endtime" name="endtime" />
                            <input type="hidden" name="id" value="{{ $device_id }}" />
                            <input type="hidden" name="excel" value="true" />
                            <div id="reportrange"
                                style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;"
                                class="col-sm-3">
                                <i class="voyager-calendar"></i>&nbsp;
                                <span>Tarih Aralığı Seçin</span> <i class="voyager-sort-desc"></i>
                            </div>
                            <div class="form-check col-sm-2">
                                <input type="checkbox" class="form-check-input" name="dif_data" value="1" id="dif_data" />
                                <label class="form-check-label" for="dif_data">İki Tarih Arası Fark Veri</label>
                            </div>
                            <div class="form-group col-sm-3">
                                <label class="control-label col-sm-offset-2 col-sm-2" for="aralik">Aralık</label>
                                <div class="col-sm-6 col-md-4">
                                    <select id="aralik" name="period" class="form-control">
                                        <option value="all">Tüm Veri</option>
                                        <option value="hour">Saatlik</option>
                                        <option value="day">Günlük</option>
                                        <option value="week">Haftalık</option>
                                        <option value="month">Aylık</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <input type="submit" class="voyager-documentation btn btn-success" value="Excel" />
                            </div>

                        </form>
                        <!-- Button trigger modal -->
                        @if ($mac == '00:00:00:00:00:01')
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#manuelVeri"
                                style="float:right">
                                Manuel Veri Ekle
                            </button>
                        @endif
                    </div>
                    <div class="panel-body"> 
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tarih</th>
                                        @foreach ($columns as $row)
                                            <th class="no-sort no-click">{{ $row }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tfood>
                                    <tr>
                                        <th>Tarih</th>
                                        @foreach ($columns as $row)
                                            <th class="no-sort no-click">{{ $row }}</th>
                                        @endforeach
                                    </tr>
                                </tfood>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="manuelVeri" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Manuel Veri Ekle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Kapat">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="manuelAdd" method="POST" action="{{ route('manuelAdd') }}">
                        @csrf
                        <input type="hidden" name="id" value="{{ $device_id }}" />
                        <div class="form-group">
                            <label for="date">Tarih</label>
                            <input type="datetime-local" class="form-control" id="date" name="date"
                                value="{{ date('Y-m-d\TH:i') }}" />
                        </div>

                        @foreach ($columns as $row)
                            <div class="form-group">
                                <label>{{ $row }}</label>
                                <input type="number" class="form-control" name="tags[]" placeholder="{{ $row }}"
                                    step="0.01" value="0" />
                            </div>
                        @endforeach

                        </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                    <button type="submit" form="manuelAdd" value="Verileri Kaydet" class="btn btn-primary">Verileri
                        Kaydet</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


@stop

@section('javascript')


    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/locale/tr.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    @php
    $columns2 = [];

    $columns2[] = ['data' => 'Tarih'];
    @endphp
    @foreach ($columns as $row)
        @php
            $columns2[] = ['data' => $row];
        @endphp
    @endforeach
    <script>
        $(document).ready(function() {

            var table = $('#dataTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "{{ route('cihazverilerajax') }}",
                    type: "POST",
                    data: function(d) {
                        d.id = "{{ $device_id }}";
                        d._token = "{{ csrf_token() }}";
                        d.starttime = $("#starttime").val();
                        d.endtime = $("#endtime").val();
                    }
                },
                "columns": {!! json_encode($columns2, true) !!},
                "language": {!! json_encode(__('voyager::datatable'), true) !!},
                "dom": 'rltip',
                "order": [
                    [0, 'desc']
                ],
                "lengthMenu": [30, 50, 100, 250, 500],
                "columnDefs": [{
                    "targets": 'no-sort',
                    "orderable": false,
                }]
            })

            function cb(start, end) {
                $("#starttime").val(start.format('YYYY/MM/DD HH:mm:ss'));
                $("#endtime").val(end.format('YYYY/MM/DD HH:mm:ss'));
                table.draw();

                var options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                $('#reportrange span').html(start.format('MMMM D, YYYY H:mm:ss') + ' - ' + end.format(
                    'MMMM D, YYYY H:mm:ss'));
            }

            $('#reportrange').daterangepicker({
                timePicker: true,
                "locale": {
                    "format": "MMMM D, YYYY H:mm",
                    "applyLabel": "Uygula",
                    "cancelLabel": "Vazgeç",
                    "fromLabel": "Başlangıç",
                    "toLabel": "Bitiş",
                    "customRangeLabel": "Manuel",
                    "daysOfWeek": [
                        "Pz",
                        "Pzt",
                        "Sl",
                        "Çr",
                        "Pr",
                        "Cm",
                        "Cmt"
                    ],
                    "monthNames": [
                        "Ocak",
                        "Şubat",
                        "Mart",
                        "Nisan",
                        "Mayıs",
                        "Haziran",
                        "Temmuz",
                        "Ağustos",
                        "Eylül",
                        "Ekim",
                        "Kasım",
                        "Aralık"
                    ],
                    "firstDay": 1
                },
                ranges: {
                    'Bugün': [moment().startOf('day'), moment().add(1, 'days').startOf('day')],
                    'Dün': [moment().subtract(1, 'days').startOf('day'), moment().startOf('day')],
                    'Önceki Gün': [moment().subtract(2, 'days').startOf('day'), moment().subtract(1, 'days')
                        .startOf('day')
                    ],
                    'Son 7 Gün': [moment().subtract(6, 'days').startOf('day'), moment().add(1, 'days')
                        .startOf('day')
                    ],
                    'Son 30 Gün': [moment().subtract(29, 'days').startOf('day'), moment().add(1, 'days')
                        .startOf('day')
                    ],
                    'Bu Ay': [moment().startOf('month').startOf('day'), moment().endOf('month')],
                    'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')],
                    'Tüm Zamanlar': [moment('2020-01-01'), moment().add(1, 'days').startOf('day')]
                }
            }, cb);




        });

    </script>

@stop
