@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing'))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class=""></i>
        </h1>

    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">

                      <table id="dataTable" class="table table-hover">
                      <thead>
                      <tr>

                      <th>Firma</th>
                        <th></th>

                      <th class="actions text-right">İşlemler</th>
                      </tr>
                      </thead>
                      <tbody>

                     @foreach ($companies as $company)
                        @if ($company)
                            <tr>
                            <td>
                                @if (!empty($company->avatar))
                                <img src="@if( !filter_var($company->avatar, FILTER_VALIDATE_URL)){{ Voyager::image($company->avatar) }}@else{{ $company->avatar }}@endif" width="50" height="50" />
                                @else
                                <img src="{{ asset('storage/users/default.png') }}" width="50" height="50" />
                                @endif
                            </td>
                            <td>{{ $company->name ?? 'Firma Adı Yok' }}</td>
                            <td class="no-sort no-click" id="bread-actions">
                                <a href="{{ route('switch_company', ['id' => $company->id]) }}" title="Geçiş Yap" class="btn btn-sm btn-warning pull-right view">
                                <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">Geçiş Yap</span>
                                </a>
                            </td>
                            </tr>
                        @endif
                    @endforeach

                      </tbody>
                      </table>
</div>
</div>
</div>
</div>
</div>

                      @stop

                      @section('css')

                      @stop

                      @section('javascript')
                          <!-- DataTables -->
                      @stop
