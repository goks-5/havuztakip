@extends('voyager::master')

@php
  switch ($status) {
    case 'tamamlanan':
      $title = "Tamamlanan Peryodik Bakımlar";
      break;
      case 'bekleyen':
        $title = "Beklemeye Alınan Peryodik Bakımlar";
        break;
        case 'guncel':
          $title = "Güncel Peryodik Bakımlar";
          break;
          case 'gelecek':
            $title = "Gelecek Peryodik Bakımlar";
            break;

    default:
      $title = "Tüm Peryodik Bakımlari";
      break;
  }
@endphp


@section('page_title',$title)

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-wand"></i> {{$title}}
        </h1>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
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
                                    @foreach($tasks as $data)
                                      @php
                                      if(isset( $data['task']->status)){
                                        $status = $data['task']->status;
                                      }else{
                                        $status = "Bekliyor";
                                      }
                                      $late = false;
                                      $gecikme = strtotime("+6 day", strtotime($data['tarih']));
                                      if($status == "Bekliyor" && strtotime('now') > $gecikme ){
                                          $late = true;
                                      }
                                    @endphp

                                    <tr>
                                      <td><span>{{ $data['id'] }}</span></td>
                                      <td><span>{{ $data['equipment']->name }}</span></td>
                                      <td><span>{{ $data['equipment']->env_code }}</span></td>
                                      <td><span>{{ $data['maintance']->title }}</span></td>
                                      <td><span @if($late) style="color:red;font-weight: bold;" @endif>{{ $data['tarih'] }}</span></td>
                                      <td><span>{{ $data['task']->task_date ?? '---' }}</span></td>
                                      <td><span>{{ $data['task']->name ?? '---'}}</span></td>
                                      <td><span>{{ $status}}</span></td>
                                        <td class="no-sort no-click" >
                                          <a href="#" title="Bakım Gir" class="btn btn-sm btn-primary pull-right edit ajaxmodal btn-kck"
                                          data-task_week_id='{{ $data['id'] }}'
                                          data-maintenance_id='{{ $data['maintance']->id}}'
                                          data-equipment_id='{{ $data['equipment']->id}}'>
                                              <i class="voyager-edit"></i>
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

    @include('widget.ajaxModal')
@stop

@section('css')


@stop

@section('javascript')

    <script>
        $(document).ready(function () {
                var table = $('#dataTable').DataTable({!! json_encode(
                    array_merge([
                        "order" => [[4,"asc"]],
                        "language" => __('voyager::datatable'),
                        "columnDefs" => [['targets' => -1, 'searchable' =>  false, 'orderable' => false]],
                    ],
                    config('voyager.dashboard.data_tables', []))
                , true) !!});

                $('.ajaxmodal').click(function(){
                  var pdata = $(this).data();
                  pdata._token = "{{csrf_token()}}";
                   $.ajax({
                    url: '{{route('task_edit')}}',
                    type: 'post',
                    data: pdata,
                    success: function(response){
                      $('.modal-title').html('Bakım Detayları');
                        $('.modal-body').html(response);
                      $('#empModal').modal('show');
                    }
                  });
                 });

        });



    </script>
@stop
