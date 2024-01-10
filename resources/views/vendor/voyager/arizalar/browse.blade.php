@extends('voyager::master')


@php
  switch (request()->get('s')) {
    case 'yeni':
      $title = "Yeni Gelen İş Emirleri";
      break;
      case 'Onay':
        $title = "Onay Bekleyen İş Emirleri";
        break;
        case '|0|':
          $title = "İşlemdeki İş Emirleri";
          break;
          case '|2|':
            $title = "Beklemeye Alınan İş emirleri";
            break;
            case '|1|':
              $title = "Tamamlanan İş Emirleri";
              break;
    default:
      $title = "Tüm İş Emirleri";
      break;
  }
@endphp


@section('page_title', $title)

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i> {{ $title }}
        </h1>
        @can('add', app($dataType->model_name))
            <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
            </a>
        @endcan
        @can('delete', app($dataType->model_name))
            @include('voyager::partials.bulk-delete')
        @endcan
        @can('edit', app($dataType->model_name))
            @if(isset($dataType->order_column) && isset($dataType->order_display_column))
                <a href="{{ route('voyager.'.$dataType->slug.'.order') }}" class="btn btn-primary btn-add-new">
                    <i class="voyager-list"></i> <span>{{ __('voyager::bread.order') }}</span>
                </a>
            @endif
        @endcan
        @can('delete', app($dataType->model_name))
            @if($usesSoftDeletes)
                <input type="checkbox" @if ($showSoftDeleted) checked @endif id="show_soft_deletes" data-toggle="toggle" data-on="{{ __('voyager::bread.soft_deletes_off') }}" data-off="{{ __('voyager::bread.soft_deletes_on') }}">
            @endif
        @endcan
        @foreach($actions as $action)
            @if (method_exists($action, 'massAction'))
                @include('voyager::bread.partials.actions', ['action' => $action, 'data' => null])
            @endif
        @endforeach
        @include('voyager::multilingual.language-selector')
    </div>
@stop

@section('content')
    @php
     $staffs = App\Staff::select("*")->where('company_id',Auth::user()->company_id)->get()

@endphp
    <div class="page-content browse container-fluid">

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Tarih Aralığı Formu</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('voyager.'.$dataType->slug.'.index') }}" method="get">
                    <!-- Query string değerleri için gizli inputlar -->
                    <input type="hidden" name="s" value="{{ request('s') }}">
                    <input type="hidden" name="key" value="{{ request('key') }}">
                    <input type="hidden" name="filter" value="{{ request('filter') }}">
    
                    <div class="form-group">
                        <label for="startdate">Başlangıç Tarihi:</label>
                        <input type="date" class="form-control" id="startdate" name="startdate" required>
                    </div>
                    <div class="form-group">
                        <label for="enddate">Bitiş Tarihi:</label>
                        <input type="date" class="form-control" id="enddate" name="enddate" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Gönder</button>
                </form>
            </div>
        </div>
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        @if ($isServerSide)
                            <form method="get" class="form-search">
                                <div id="search-input">
                                    <div class="col-2">
                                        <select id="search_key" name="key">
                                            @foreach($searchNames as $key => $name)
                                                <option value="{{ $key }}" @if($search->key == $key || (empty($search->key) && $key == $defaultSearchKey)){{ 'selected' }}@endif>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <select id="filter" name="filter">
                                            <option value="contains" @if($search->filter == "contains"){{ 'selected' }}@endif>contains</option>
                                            <option value="equals" @if($search->filter == "equals"){{ 'selected' }}@endif>=</option>
                                        </select>
                                    </div>
                                    <div class="input-group col-md-12">
                                        <input type="text" class="form-control" placeholder="{{ __('voyager::generic.search') }}" name="s" value="{{ $search->value }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-info btn-lg" type="submit">
                                                <i class="voyager-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                @if (Request::has('sort_order') && Request::has('order_by'))
                                    <input type="hidden" name="sort_order" value="{{ Request::get('sort_order') }}">
                                    <input type="hidden" name="order_by" value="{{ Request::get('order_by') }}">
                                @endif
                            </form>
                        @endif

                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        @if($showCheckboxColumn)
                                            <th>
                                                <input type="checkbox" class="select_all">
                                            </th>
                                        @endif
                                        @foreach($dataType->browseRows as $row)
                                        <th>
                                            @if ($isServerSide)
                                                <a href="{{ $row->sortByUrl($orderBy, $sortOrder) }}">
                                            @endif
                                            {{ $row->getTranslatedAttribute('display_name') }}
                                            @if ($isServerSide)
                                                @if ($row->isCurrentSortField($orderBy))
                                                    @if ($sortOrder == 'asc')
                                                        <i class="voyager-angle-up pull-right"></i>
                                                    @else
                                                        <i class="voyager-angle-down pull-right"></i>
                                                    @endif
                                                @endif
                                                </a>
                                            @endif
                                        </th>
                                        @endforeach
                                        <th class="actions text-right">{{ __('voyager::generic.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTypeContent as $data)
                                      @php
                                      switch ($data->status) {
                                      case 'Bekliyor |0|':
                                      $fontcolor = "#526069";
                                      $cellcolor = "#FF5";
                                      break;
                                      case 'Bakıma Başlandı |0|':
                                      $fontcolor = "#526069";
                                      $cellcolor = "#FF8";
                                      break;
                                      case 'Firma Yönlendirildi |2|':
                                      $fontcolor = "#526069";
                                      $cellcolor = "#aef";
                                      break;
                                      case 'Malzeme Bekliyor |2|':
                                      $fontcolor = "#526069";
                                      $cellcolor = "#aef";
                                      break;
                                      case 'Onay |1|':
                                      $cellcolor = "#9F9";
                                      $fontcolor = "#526069";
                                      break;
                                      case 'Yeni':
                                      $cellcolor = "#F33";
                                      $fontcolor = "#FFF";
                                      break;
                                      default:
                                      $cellcolor = "#FFF";
                                      $fontcolor = "#526069";
                                      break;
                                      }
                                      @endphp

                                      <tr style="background:{{$cellcolor}};color:{{$fontcolor}}">
                                        @if($showCheckboxColumn)
                                            <td>
                                                <input type="checkbox" name="row_id" id="checkbox_{{ $data->getKey() }}" value="{{ $data->getKey() }}">
                                            </td>
                                        @endif
                                        @foreach($dataType->browseRows as $row)
                                            @php
                                            if ($data->{$row->field.'_browse'}) {
                                                $data->{$row->field} = $data->{$row->field.'_browse'};
                                            }
                                            @endphp
                                            <td>
                                                @if (isset($row->details->view))
                                                    @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $data->{$row->field}, 'action' => 'browse'])
                                                @elseif($row->type == 'image')
                                                    <img src="@if( !filter_var($data->{$row->field}, FILTER_VALIDATE_URL)){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif" style="width:100px">
                                                @elseif($row->type == 'relationship')
                                                    @include('voyager::formfields.relationship', ['view' => 'browse','options' => $row->details])
                                                @elseif($row->type == 'select_multiple')
                                                    @if(property_exists($row->details, 'relationship'))

                                                        @foreach($data->{$row->field} as $item)
                                                            {{ $item->{$row->field} }}
                                                        @endforeach

                                                    @elseif(property_exists($row->details, 'options'))
                                                        @if (!empty(json_decode($data->{$row->field})))
                                                            @foreach(json_decode($data->{$row->field}) as $item)
                                                                @if (@$row->details->options->{$item})
                                                                    {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            {{ __('voyager::generic.none') }}
                                                        @endif
                                                    @endif

                                                    @elseif($row->type == 'multiple_checkbox' && property_exists($row->details, 'options'))
                                                        @if (@count(json_decode($data->{$row->field})) > 0)
                                                            @foreach(json_decode($data->{$row->field}) as $item)
                                                                @if (@$row->details->options->{$item})
                                                                    {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            {{ __('voyager::generic.none') }}
                                                        @endif

                                                @elseif(($row->type == 'select_dropdown' || $row->type == 'radio_btn') && property_exists($row->details, 'options'))

                                                    {!! $row->details->options->{$data->{$row->field}} ?? '' !!}

                                                @elseif($row->type == 'date' || $row->type == 'timestamp')
                                                    @if ( property_exists($row->details, 'format') && !is_null($data->{$row->field}) )
                                                        {{ \Carbon\Carbon::parse($data->{$row->field})->formatLocalized($row->details->format) }}
                                                    @else
                                                        {{ $data->{$row->field} }}
                                                    @endif
                                                @elseif($row->type == 'checkbox')
                                                    @if(property_exists($row->details, 'on') && property_exists($row->details, 'off'))
                                                        @if($data->{$row->field})
                                                            <span class="label label-info">{{ $row->details->on }}</span>
                                                        @else
                                                            <span class="label label-primary">{{ $row->details->off }}</span>
                                                        @endif
                                                    @else
                                                    {{ $data->{$row->field} }}
                                                    @endif
                                                @elseif($row->type == 'color')
                                                    <span class="badge badge-lg" style="background-color: {{ $data->{$row->field} }}">{{ $data->{$row->field} }}</span>
                                                @elseif($row->type == 'text')
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <div>{{ mb_strlen( $data->{$row->field} ) > 200 ? mb_substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</div>
                                                @elseif($row->type == 'text_area')
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <div>{{ mb_strlen( $data->{$row->field} ) > 200 ? mb_substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</div>
                                                @elseif($row->type == 'file' && !empty($data->{$row->field}) )
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    @if(json_decode($data->{$row->field}) !== null)
                                                        @foreach(json_decode($data->{$row->field}) as $file)
                                                            <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}" target="_blank">
                                                                {{ $file->original_name ?: '' }}
                                                            </a>
                                                            <br/>
                                                        @endforeach
                                                    @else
                                                        <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($data->{$row->field}) }}" target="_blank">
                                                            Download
                                                        </a>
                                                    @endif
                                                @elseif($row->type == 'rich_text_box')
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <div>{{ mb_strlen( strip_tags($data->{$row->field}, '<b><i><u>') ) > 200 ? mb_substr(strip_tags($data->{$row->field}, '<b><i><u>'), 0, 200) . ' ...' : strip_tags($data->{$row->field}, '<b><i><u>') }}</div>
                                                @elseif($row->type == 'coordinates')
                                                    @include('voyager::partials.coordinates-static-image')
                                                @elseif($row->type == 'multiple_images')
                                                    @php $images = json_decode($data->{$row->field}); @endphp
                                                    @if($images)
                                                        @php $images = array_slice($images, 0, 3); @endphp
                                                        @foreach($images as $image)
                                                            <img src="@if( !filter_var($image, FILTER_VALIDATE_URL)){{ Voyager::image( $image ) }}@else{{ $image }}@endif" style="width:50px">
                                                        @endforeach
                                                    @endif
                                                @elseif($row->type == 'media_picker')
                                                    @php
                                                        if (is_array($data->{$row->field})) {
                                                            $files = $data->{$row->field};
                                                        } else {
                                                            $files = json_decode($data->{$row->field});
                                                        }
                                                    @endphp
                                                    @if ($files)
                                                        @if (property_exists($row->details, 'show_as_images') && $row->details->show_as_images)
                                                            @foreach (array_slice($files, 0, 3) as $file)
                                                            <img src="@if( !filter_var($file, FILTER_VALIDATE_URL)){{ Voyager::image( $file ) }}@else{{ $file }}@endif" style="width:50px">
                                                            @endforeach
                                                        @else
                                                            <ul>
                                                            @foreach (array_slice($files, 0, 3) as $file)
                                                                <li>{{ $file }}</li>
                                                            @endforeach
                                                            </ul>
                                                        @endif
                                                        @if (count($files) > 3)
                                                            {{ __('voyager::media.files_more', ['count' => (count($files) - 3)]) }}
                                                        @endif
                                                    @elseif (is_array($files) && count($files) == 0)
                                                        {{ trans_choice('voyager::media.files', 0) }}
                                                    @elseif ($data->{$row->field} != '')
                                                        @if (property_exists($row->details, 'show_as_images') && $row->details->show_as_images)
                                                            <img src="@if( !filter_var($data->{$row->field}, FILTER_VALIDATE_URL)){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif" style="width:50px">
                                                        @else
                                                            {{ $data->{$row->field} }}
                                                        @endif
                                                    @else
                                                        {{ trans_choice('voyager::media.files', 0) }}
                                                    @endif
                                                @else
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <span>{{ $data->{$row->field} }}</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="no-sort no-click" id="bread-actions" style="min-width: 150px;">


                                          @can('accept',app('App\Fault'))

                                            @if($data->status == 'Yeni')
                                              <a href="#" title="Kabul Et" data-id ='{{$data->id}}' class="btn btn-sm btn-danger pull-right edit modalidset" data-toggle="modal"  data-target="#acceptModal" >
                                              <i class="voyager-paper-plane"></i> <span class="hidden-xs hidden-sm">Kabul Et</span>
                                              </a>
                                              @endif
                                            @if($data->status <> 'Yeni' && $data->status <> 'Onay |1|' && $data->status <> 'Bitti |1|')
                                              <a href="#" title="İşlem Gir" data-id ='{{$data->id}}'  class="btn btn-sm btn-warning pull-right edit modalidset" data-toggle="modal"  data-target="#actionModal">
                                              <i class="voyager-fire"></i> <span class="hidden-xs hidden-sm">İşlem Gir</span>
                                              </a>
                                            @endif
                                          @endcan
                                          @can('close',app('App\Fault'))
                                            @if($data->status == 'Onay |1|')
                                              <a href="#" title="Arızayı Kapat" data-id ='{{$data->id}}'  class="btn btn-sm btn-primary pull-right edit modalidset" data-toggle="modal"  data-target="#closeModal">
                                              <i class="voyager-lightbulb"></i> <span class="hidden-xs hidden-sm">Arızayı Kapat</span>
                                              </a>
                                            @endif
                                          @endcan
                                          @foreach($actions as $action)
                                              @if (!method_exists($action, 'massAction'))
                                                  @include('voyager::bread.partials.actions', ['action' => $action])
                                              @endif
                                          @endforeach
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($isServerSide)
                            <div class="pull-left">
                                <div role="status" class="show-res" aria-live="polite">{{ trans_choice(
                                    'voyager::generic.showing_entries', $dataTypeContent->total(), [
                                        'from' => $dataTypeContent->firstItem(),
                                        'to' => $dataTypeContent->lastItem(),
                                        'all' => $dataTypeContent->total()
                                    ]) }}</div>
                            </div>
                            <div class="pull-right">
                                {{ $dataTypeContent->appends([
                                    's' => $search->value,
                                    'filter' => $search->filter,
                                    'key' => $search->key,
                                    'order_by' => $orderBy,
                                    'sort_order' => $sortOrder,
                                    'showSoftDeleted' => $showSoftDeleted,
                                ])->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="acceptModal" role="dialog">
     <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
       <div class="modal-header">
         <h4 class="modal-title">Arızayı Kabul Et</h4>
         <button type="button" class="close" data-dismiss="modal">&times;</button>
       </div>
       <div class="modal-body">
         <form action="{{route('faultsActions')}}"  method="POST">
             {{ csrf_field()}}
             <input type="hidden" name='action' value='accept' />
               <input type="hidden" name='id' class='modalidinput' />
             <div class="form-group row">
              <label for="maintainer_id" class="col-md-4">Bakımcı</label>
             <select class="selector col-md-8" name='maintainer_id'>
               <option value=''>Bakım Elemanı Seçin</option>
               @foreach ($staffs as $staff)
                 <option value='{{$staff->id}}'>{{$staff->name}}</option>
               @endforeach
             </select>
           </div>

             <input type="submit" class="btn btn-danger pull-right delete-confirm" value="Kabul Et">
         </form>
       </div>
       <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
       </div>
      </div>
     </div>
    </div>

    <div class="modal fade" id="actionModal" role="dialog">
     <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
       <div class="modal-header">
         <h4 class="modal-title">Arıza İşlemi</h4>
         <button type="button" class="close" data-dismiss="modal">&times;</button>
       </div>
       <div class="modal-body">
         <form action="{{route('faultsActions')}}"  method="POST">

           <input type="hidden" name='action' value='action' />

             <input type="hidden" name='id' class='modalidinput' />
             <div class="form-group row">
              <label for="status" class="col-md-4">İşlem</label>
           <select class="selector col-md-8" name='status'>

               <option value='Bekliyor |0|'>Bekliyor</option>
               <option value='Bakıma Başlandı |0|'>Bakıma Başlandı</option>
               <option value='Firma Yönlendirildi |2|'>Firma Yönlendirildi</option>
               <option value='Malzeme Bekliyor |2|'>Malzeme Bekliyor</option>
               <option value='Onay |1|'>Tamamlandı</option>

           </select>
</div>
<div class="form-group row">
 <label for="note" class="col-md-4">Açıklama</label>
           <textarea name="maintainer_note" class="col-md-8" rows="6"></textarea>
         </div>
             {{ csrf_field()}}
             <input type="hidden" name='id' class='modalidinput' />
             <input type="submit" class="btn btn-danger pull-right delete-confirm" value="İşlem Gir">
         </form>
       </div>
       <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
       </div>
      </div>
     </div>
    </div>

    <div class="modal fade" id="closeModal" role="dialog">
     <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
       <div class="modal-header">
         <h4 class="modal-title">Arızayı Kapat</h4>
         <button type="button" class="close" data-dismiss="modal">&times;</button>
       </div>
       <div class="modal-body">
         <form action="{{route('faultsActions')}}"  method="POST">
             {{ csrf_field()}}

                        <input type="hidden" name='action' value='close' />
             <input type="hidden" name='id' class='modalidinput' />
             <input type="submit" class="btn btn-danger pull-right delete-confirm" value="Arızayı Kapat">
         </form>
       </div>
       <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
       </div>
      </div>
     </div>
    </div>


    {{-- Single delete modal --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} {{ strtolower($dataType->getTranslatedAttribute('display_name_singular')) }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="#" id="delete_form" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm" value="{{ __('voyager::generic.delete_confirm') }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop

@section('css')
@if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
    <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
@endif

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css">
@stop

@section('javascript')
    <!-- DataTables -->
    @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    @endif
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
    @php
      $pcolums = array();
    @endphp
  @foreach($dataType->browseRows as $row)
    @php
      $pcolums[] = $loop->iteration ;
    @endphp
      @endforeach
    <script>
    $('.modalidset').click(function(){
      console.log($(this).data('id'));
    $('.modalidinput').val($(this).data('id'));
    });
            @if (!$dataType->server_side)
        //    $('#dataTable thead tr').clone(true).appendTo( '#dataTable thead' );
            $('#dataTable thead tr th').each( function (i) {
                var title = $(this).text().trim();
                if( title.length  && title != 'İşlemler' && title != 'D' ){
                  $(this).html( title+'<input type="text" placeholder="'+title+'" size="'+title.length+'" />' );

                  $( 'input', this ).on( 'keyup change', function () {
                      if ( table.column(i).search() !== this.value ) {
                          table
                              .column(i)
                              .search( this.value )
                              .draw();
                      }
                  } );
                }else{
                  $(this).html('');
                }

            } );
                var table = $('#dataTable').DataTable({!! json_encode(
                    array_merge([
                        "order" => [[ 7, "desc" ]],
                        "pageLength" => 100,
                        "dom"=> 'Blfrtip',
                        "buttons" => [[
                'extend'=>'excelHtml5',
                'exportOptions'=>['columns'=> $pcolums]
            ],[
                'extend'=>'print',
                'exportOptions'=>['columns'=> $pcolums],
                 'orientation'=> 'landscape'
             ]],
                        "language" => __('voyager::datatable'),
                        "columnDefs" => [['targets' => -1, 'searchable' =>  false, 'orderable' => false],['targets' => 1, 'width'=>'10px']],
                    ],
                    config('voyager.dashboard.data_tables', []))
                , true) !!});
            @else
                $('#search-input select').select2({
                    minimumResultsForSearch: Infinity
                });
            @endif

            @if ($isModelTranslatable)
                $('.side-body').multilingual();
                //Reinitialise the multilingual features when they change tab
                $('#dataTable').on('draw.dt', function(){
                    $('.side-body').data('multilingual').init();
                })
            @endif
            $('.select_all').on('click', function(e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked')).trigger('change');
            });


        var deleteFormAction;
        $('td').on('click', '.delete', function (e) {
            $('#delete_form')[0].action = '{{ route('voyager.'.$dataType->slug.'.destroy', '__id') }}'.replace('__id', $(this).data('id'));
            $('#delete_modal').modal('show');
        });

        @if($usesSoftDeletes)
            @php
                $params = [
                    's' => $search->value,
                    'filter' => $search->filter,
                    'key' => $search->key,
                    'order_by' => $orderBy,
                    'sort_order' => $sortOrder,
                ];
            @endphp
            $(function() {
                $('#show_soft_deletes').change(function() {
                    if ($(this).prop('checked')) {
                        $('#dataTable').before('<a id="redir" href="{{ (route('voyager.'.$dataType->slug.'.index', array_merge($params, ['showSoftDeleted' => 1]), true)) }}"></a>');
                    }else{
                        $('#dataTable').before('<a id="redir" href="{{ (route('voyager.'.$dataType->slug.'.index', array_merge($params, ['showSoftDeleted' => 0]), true)) }}"></a>');
                    }

                    $('#redir')[0].click();
                })
            })
        @endif
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
