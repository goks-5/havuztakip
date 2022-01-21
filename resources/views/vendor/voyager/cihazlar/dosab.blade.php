@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' Dosab Sayaç Ekle')

@section('page_header')
<div class="container-fluid">
  <h1 class="page-title">
    <i class="voyager-bar-chart"></i> Dosab Sayaç Ekle
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

          <form role="form" class="form-edit-add" action="{{route('dosabkaydet')}}" method="POST" enctype="multipart/form-data">
  @csrf

            <div class="panel-body">



@if(isset($device->id ))
<input type="hidden" name='id' value="{{$device->id}}"/>
@php
  $values = explode("_",str_replace('DOSAB_', '', $device->device_id));
  $diff_tags = json_decode($device->diff_tags,true);
@endphp
@endif

              <div class="form-group  col-md-12 ">
                <label class="control-label" for="name">Takma Ad</label>
                <input type="text" class="form-control" name="name" placeholder="Takma Ad" value="{{ isset($device->name ) ? $device->name  : '' }}">
              </div>
              <div class="form-group  col-md-12 ">
                <label class="control-label" for="device_id">Sayaç id'si</label>
                <input type="number" class="form-control" name="device_id" placeholder="Sayaç id'si" value="{{ isset($values[0]  ) ?  $values[0]    : '' }}">
              </div>
              <div class="form-group  col-md-12 ">
              <label class="control-label" for="device_type">Sayaç Tipi</label>
              <select class="form-control select2" name="device_type">
              <option value="0" {{isset($values[1]) && $values[1] == 0 ? 'selected' : ''}}>Elektrik Sayacı</option>
              <option value="1" {{isset($values[1]) && $values[1] == 1 ? 'selected' : ''}}>Doğalgaz Sayacı</option>
              <option value="2" {{isset($values[1]) && $values[1] == 2 ? 'selected' : ''}}>İçme Suyu Sayacı</option>
              <option value="3" {{isset($values[1]) && $values[1] == 3 ? 'selected' : ''}}>Proses Suyu Sayacı</option>
              <option value="4" {{isset($values[1]) && $values[1] == 4 ? 'selected' : ''}}>Atık Su Sayacı</option>
              </select>
              </div>
              
              <div class="col-sm-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value=" Saatlik" id="t_100" name="diff_tags[100]" {{isset($diff_tags[100])  ? 'checked' : ''}}>
                  <label class="form-check-label" for="t_100">Saatlik</label>
                </div>
                </div>
              <div class="col-sm-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value=" Günlük" id="t_200" name="diff_tags[200]" {{isset($diff_tags[200])  ? 'checked' : ''}}>
              <label class="form-check-label" for="t_200">Günlük</label>
            </div>
            </div>
              <div class="col-sm-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value=" Haftalık" id="t_300" name="diff_tags[300]" {{isset($diff_tags[300])  ? 'checked' : ''}}>
              <label class="form-check-label" for="t_300">Haftalık</label>
            </div>
            </div>
              <div class="col-sm-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox"  value=" Aylık" id="t_400" name="diff_tags[400]" {{isset($diff_tags[400])  ? 'checked' : ''}}>
              <label class="form-check-label" for="t_400">Aylık</label>
            </div>
          </div>


            </div>
            <div class="panel-footer">
              <button type="submit" class="btn btn-primary save">Kaydet</button>
            </div>
          </form>


        </div>
      </div>
    </div>
  </div>
</div>


@stop

@section('css')


@stop

@section('javascript')




@stop
