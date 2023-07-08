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
                    <div class="panel-body">
                        @if ($string ?? false)
                            @dump($string)
                        @endif
                        @if ($number ?? false)
                            @dump($number)
                        @endif


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
        
    </script>

@stop
