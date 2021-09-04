@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' Cihaz Verileri')

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
        <div class="panel-body">
          <div class="table-responsive">
            <table id="dataTable" class="table table-hover">
              <thead>
                <tr>
                  @foreach($culumns as $row)
                  <th>{{ $row}}</th>
                  @endforeach
                  <th class="actions text-right">{{ __('voyager::generic.actions') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($devices as $data)
                  @php
                  $timeout1 =  setting('device.ofline') * 60;
                  $timeout2 =  setting('device.oflinesayac');
                  if($data->mac == '00:00:00:00:00:01'){
                    $timeout = $timeout2;
                  }else{
                      $timeout = $timeout1;
                  }
                      if ((strtotime($data->last_at) + $timeout < strtotime('now')) && $data->mac <> '00:00:00:00:00:02' ) {
                        $ofline = true;
                      }else{
                        $ofline = false;
                      }
                @endphp

                <tr @if($ofline) style="background-color: #a20000;    color: white;" @endif>
                  <td><span>{{ $data->device_id }}</span></td>
                  <td><span>{{ $data->name }}</span></td>
                  <td><span>{{ $data->last_at }}</span></td>

                  <td>
                    @php
                    $tags = json_decode($data->tags , true);
                    $last_data = json_decode($data->last_data , true);
                    @endphp

                    @if(is_array($tags))
                        @foreach($tags as $key => $tag)
                        <span style="padding-right:10px" class="col-xs-4"><b>{{$tag}} :</b> {{$last_data[$key] ?? ''}} </span>
                        @endforeach
                    @endif
                  </td>
                  <td class="no-sort no-click" id="bread-actions">
                    <a href="{!! route('cihazveriler',['id' => $data->id]) !!}" title="Tüm Veriler" class="btn btn-sm btn-warning pull-right view">
                    <i class="voyager-archive"></i> <span class="hidden-xs hidden-sm">Tüm Veriler</span>
                    </a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>


@stop

@section('css')


@stop

@section('javascript')

      <script>
          $(document).ready(function () {
                  var table = $('#dataTable').DataTable({!! json_encode(
                      array_merge([
                          "order" => [[1,"asc"]],
                          "language" => __('voyager::datatable'),
                          "columnDefs" => [['targets' => -1, 'searchable' =>  false, 'orderable' => false]],
                      ],
                      config('voyager.dashboard.data_tables', []))
                  , true) !!});

              $('.select_all').on('click', function(e) {
                  $('input[name="row_id"]').prop('checked', $(this).prop('checked')).trigger('change');
              });
          });


          $('input[name="row_id"]').on('change', function () {
              var ids = [];
              $('input[name="row_id"]').each(function() {
                  if ($(this).is(':checked')) {
                      ids.push($(this).val());
                  }
              });
              $('.selected_ids').val(ids);
          });
      </script>

@stop
