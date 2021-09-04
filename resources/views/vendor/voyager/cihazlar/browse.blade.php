@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.$dataType->getTranslatedAttribute('display_name_plural'))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i> {{ $dataType->getTranslatedAttribute('display_name_plural') }}
        </h1>
        @can('virtual', app($dataType->model_name))
            <a href="{{ route('sanalekle') }}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>Sanal Cihaz Ekle</span>
            </a>
        @endcan
        @can('dosab', app($dataType->model_name))
            <a href="{{ route('dosabekle') }}" class="btn btn-warning btn-add-new">
                <i class="voyager-plus"></i> <span>DOSAB Sayaç Ekle</span>
            </a>
        @endcan
        @can('manuel', app($dataType->model_name))
            <a href="{{ route('manuelekle') }}" class="btn btn-primary btn-add-new">
                <i class="voyager-plus"></i> <span>Manuel Girdi Ekle</span>
            </a>
        @endcan
        @can('remote', app($dataType->model_name))
            <a href="#" class="btn btn-warning btn-add-new" data-toggle="modal" data-target="#remoteModal">
                <i class="voyager-plus"></i> <span>Uzak Cihaz Ekle</span>
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
    <div class="page-content browse container-fluid">
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
                                      $timeout1 =  setting('device.ofline') * 60;
                                      $timeout2 =  setting('device.oflinesayac');
                                      if($data->mac == '00:00:00:00:00:01'){
                                        $timeout = $timeout2;
                                      }else{
                                          $timeout = $timeout1;
                                      }
                                          if (strtotime($data->last_at) + $timeout < strtotime('now') && $data->mac <> '00:00:00:00:00:02') {
                                            $ofline = true;
                                          }else{
                                            $ofline = false;
                                          }
                                    @endphp

                                    <tr @if($ofline) style="background-color: #a20000;    color: white;" @endif>
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
                                                @elseif($row->type == 'multiple_text')
                                                     {!! implode("<br> \n" ,(array) json_decode($data->{$row->field})) !!}
                                                @else
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <span>{{ $data->{$row->field} }}</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="no-sort no-click" id="bread-actions">
                                            @foreach ($actions as $action)
                                            @if ($data->mac == '00:00:00:00:00:00' || $data->mac == '00:00:00:00:00:01' || $data->mac == '00:00:00:00:00:02' || $data->mac == '00:00:00:00:00:03')
                                                @php
                                                    $class = get_class($action);
                                                    $actiona = new $class($dataType, $data);
                                                @endphp
                                                @if (get_class($actiona) == 'TCG\Voyager\Actions\EditAction')
                                                    @can('virtual', app($dataType->model_name))
                                                        @if ($data->mac == '00:00:00:00:00:00')
                                                            <a href="{{ route('sanalekle', ['id' => $data->id]) }}" title="{{ $actiona->getTitle() }}" {!!
                                                                $actiona->convertAttributesToHtml() !!}>
                                                                <i class="{{ $actiona->getIcon() }}"></i> <span
                                                                    class="hidden-xs hidden-sm">{{ $actiona->getTitle() }}</span>
                                                            </a>
                                                        @endif
                                                    @endcan
                                                    @can('dosab', app($dataType->model_name))
                                                        @if ($data->mac == '00:00:00:00:00:01')
                                                            <a href="{{ route('dosabekle', ['id' => $data->id]) }}" title="{{ $actiona->getTitle() }}" {!!
                                                                $actiona->convertAttributesToHtml() !!}>
                                                                <i class="{{ $actiona->getIcon() }}"></i> <span
                                                                    class="hidden-xs hidden-sm">{{ $actiona->getTitle() }}</span>
                                                            </a>
                                                        @endif 
                                                    @endcan
                                                    @can('dosab', app($dataType->model_name))
                                                        @if ($data && $data->mac == '00:00:00:00:00:02')
                                               
                                                            <a href="{{ $actiona->getRoute($dataType->name) }}" title="{{ $actiona->getTitle() }}" {!!
                                                                $actiona->convertAttributesToHtml() !!}>
                                                                <i class="{{ $actiona->getIcon() }}"></i> <span
                                                                    class="hidden-xs hidden-sm">{{ $actiona->getTitle() }}</span>
                                                            </a>
                                                            @php
                                                          //  dump($action);
                                                        @endphp 
                                                        @endif
                                                    @endcan
                                                    @can('dosab', app($dataType->model_name))
                                                        @if ($data->mac == '00:00:00:00:00:03')
                                                            <a href="{{ $actiona->getRoute($dataType->name) }}" title="{{ $actiona->getTitle() }}" {!!
                                                                $actiona->convertAttributesToHtml() !!}>
                                                                <i class="{{ $actiona->getIcon() }}"></i> <span
                                                                    class="hidden-xs hidden-sm">{{ $actiona->getTitle() }}</span>
                                                            </a>
                                                        @endif
                                                    @endcan
                                                @else
                                                @if (get_class($actiona) == 'TCG\Voyager\Actions\DeleteAction')
                                        
                                                        @can('virtual', app($dataType->model_name))
                                                            @if ($data->mac == '00:00:00:00:00:00')
                                                            <a href="{{ $action->getRoute($dataType->name) }}" title="{{ $actiona->getTitle() }}" {!!
                                                                $actiona->convertAttributesToHtml() !!}>
                                                                <i class="{{ $actiona->getIcon() }}"></i> <span
                                                                    class="hidden-xs hidden-sm">{{ $actiona->getTitle() }}</span>
                                                            </a> 
                                                            @endif
                                                        @endcan 
                                                        @can('dosab', app($dataType->model_name))
                                                            @if ($data->mac == '00:00:00:00:00:01')
                                                            <a href="{{ $action->getRoute($dataType->name) }}" title="{{ $actiona->getTitle() }}" {!!
                                                                $actiona->convertAttributesToHtml() !!}>
                                                                <i class="{{ $actiona->getIcon() }}"></i> <span
                                                                    class="hidden-xs hidden-sm">{{ $actiona->getTitle() }}</span>
                                                            </a> 
                                                            @endif 
                                                        @endcan  
                                                        @can('dosab', app($dataType->model_name))
                                                            @if ($data->mac == '00:00:00:00:00:02')
                                                            <a href="{{ $action->getRoute($dataType->name) }}" title="{{ $actiona->getTitle() }}" {!!
                                                                $actiona->convertAttributesToHtml() !!}>
                                                                <i class="{{ $actiona->getIcon() }}"></i> <span
                                                                    class="hidden-xs hidden-sm">{{ $actiona->getTitle() }}</span>
                                                            </a> 
                                                            @endif
                                                        @endcan
                                                        @can('dosab', app($dataType->model_name))
                                                            @if ($data->mac == '00:00:00:00:00:03')
                                                            <a href="{{ $action->getRoute($dataType->name) }}" title="{{ $actiona->getTitle() }}" {!!
                                                                $actiona->convertAttributesToHtml() !!}>
                                                                <i class="{{ $actiona->getIcon() }}"></i> <span
                                                                    class="hidden-xs hidden-sm">{{ $actiona->getTitle() }}</span>
                                                            </a> 
                                                            @endif
                                                        @endcan
                                        
                                        
                                                    @endif
                                                @endif
                                            @else
                                                @if (!method_exists($action, 'massAction'))
                                                    @include('voyager::bread.partials.actions', ['action' => $action])
                                                @endif
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
 <!--  uzak cihaz ekle-->
    <div class="modal modal-warning fade" tabindex="-1" id="remoteModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Uzak cihaz eklemek için token girin<i class="voyager-add"></i></h4>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('remoteAdd') }}" id="remote_form" method="GET">
                      <div class="form-group  col-md-12 ">
                        <label class="control-label" for="name">Token Girin</label>
                        <input type="text" class="form-control" name="token" placeholder="Token" required>
                      </div>

                        <input type="submit" class="btn btn-warning pull-right" value="Uzak Cihaz Ekle">
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
      $br = '$br';
    @endphp
  @endforeach
    <script>
        $(document).ready(function () {
            @if (!$dataType->server_side)
               var options = {!! json_encode(
                    array_merge([
                        "order" => $orderColumn,
                                           "pageLength" => 25,
                                            "dom"=> 'Blfrtip',
                                            "buttons" => [[
                                    'extend'=>'excelHtml5',
                                    'exportOptions'=>['columns'=> $pcolums ,'format'=>['body'=>null]],
                                   
                                ],[
                                    'extend'=>'print',
                                    'exportOptions'=>['columns'=> $pcolums],
                                    'orientation'=> 'landscape'
                                 ]],
                        "language" => __('voyager::datatable'),
                        "columnDefs" => [['targets' => -1, 'searchable' =>  false, 'orderable' => false]],
                    ],
                    config('voyager.dashboard.data_tables', []))
                ) !!}
                var rowsLenght = [];
                options.buttons[0].exportOptions.format.body =  function ( data, row, column, node ) {
                                 var dom = new DOMParser().parseFromString(data, 'text/html'); 
                                 data = dom.body.textContent;
                            if (column === 3) {
                                //need to change double quotes to single
                                data = data.replace( /"/g, "'" );
                                //split at each new line
                                splitData = data.split('\n');
                                data = '';
                                rowsLenght[rowsLenght.length]= {'row':row +2, 'lenght': splitData.length};
                                for (i=0; i < splitData.length; i++) {
                                    //add escaped double quotes around each line
                                    space = "";
                                    for(z=0; z < 65 - splitData[i].length ;z++){
                                        space += " ";
                                    }

                                    data += '\"' + splitData[i] + space +'\"';
                                    //if its not the last line add CHAR(13)
                                    if (i + 1 < splitData.length) {
                                      data += ', CHAR(13), ';
                                    }
                                }
                                //Add concat function
                                data = 'CONCATENATE(' + data + ')';
                            }
                            return data;
                        }
                options.buttons[0].customize = function( xlsx ) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var col = $('col', sheet);
                    var row = $('row', sheet);
                    //set the column width otherwise it will be the length of the line without the newlines
                    for (i=0; i < rowsLenght.length; i++) {
                        currentData = rowsLenght[i];
                        $(row[currentData.row]).attr('ht', (currentData.lenght * 11.5) + 3 ).attr('customHeight', "2");
                       
                    }
                     $(col[3]).attr('width', 65);
                    $('row c[r^="D"]', sheet).each(function() {
                        if ($('is t', this).text() && $('is t', this).text().includes("CONCATENATE")) {
                            console.log( $(this));
                            //wrap text
                            $(this).attr('s', '65');
                            //change the type to `str` which is a formula
                            $(this).attr('t', 'str');
                            //append the concat formula
                            $(this).append('<f>' + $('is t', this).text() + '</f>');
                            //remove the inlineStr
                            $('is', this).remove();
                        }
                    })
                }
                
                var table = $('#dataTable').DataTable(options);
                            
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
