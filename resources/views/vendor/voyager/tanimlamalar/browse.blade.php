@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' Firma  Tanımlamaları')

@section('page_header')
<div class="container-fluid">
  <h1 class="page-title">
<i class="voyager-wallet"></i>
Firma Tanımlamaları
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


        <form role="form" class="form-edit-add" action="{{route('voyager.tanimlamalar.index')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="panel-body">


            <!-- Adding / Editing -->

            <!-- GET THE DISPLAY OPTIONS -->

            <div class="form-group  col-md-12 ">

              <label class="control-label" for="name">Gün Başlangıç Saati</label>
              <select class="form-control select2" name="day_start_hour">
                <option value="0">0:00</option>
                <option value="1">1:00</option>
                <option value="2">2:00</option>
                <option value="3">3:00</option>
                <option value="4">4:00</option>
                <option value="5">5:00</option>
                <option value="6">6:00</option>
                <option value="7">7:00</option>
                <option value="8">8:00</option>
                <option value="9">9:00</option>
                <option value="10">10:00</option>
                <option value="11">11:00</option>
                <option value="12">12:00</option>
                <option value="13">13:00</option>
                <option value="14">14:00</option>
                <option value="15">15:00</option>
                <option value="16">16:00</option>
                <option value="17">17:00</option>
                <option value="18">18:00</option>
                <option value="19">19:00</option>
                <option value="20">20:00</option>
                <option value="21">21:00</option>
                <option value="22">22:00</option>
                <option value="23">23:00</option>
              </select>


            </div>
            <!-- GET THE DISPLAY OPTIONS -->

            <div class="form-group  col-md-12 ">

              <label class="control-label" for="name">Hafta Başlangıç Günü</label>
              <select class="form-control select2" name="week_start_day">
                <option value="0">Pazar</option>
                <option value="1">Pazartesi</option>
                <option value="2">Salı</option>
                <option value="3">Çarşamba</option>
                <option value="4">Perşembe</option>
                <option value="5">Cuma</option>
                <option value="6">Cumartesi</option>
              </select>


            </div>
            <!-- GET THE DISPLAY OPTIONS -->

            <div class="form-group  col-md-12 ">

              <label class="control-label" for="name">Ay Başlangıç Günü</label>
              <select class="form-control select2" name="month_start_day">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="13">13</option>
                <option value="14">14</option>
                <option value="15">15</option>
                <option value="16">16</option>
                <option value="17">17</option>
                <option value="18">18</option>
                <option value="19">19</option>
                <option value="20">20</option>
                <option value="21">21</option>
                <option value="22">22</option>
                <option value="23">23</option>
                <option value="24">24</option>
                <option value="25">25</option>
                <option value="26">26</option>
                <option value="27">27</option>
                <option value="28">28</option>
              </select>


            </div>

          </div><!-- panel-body -->

          <div class="panel-footer">
            <button type="submit" class="btn btn-primary save">Kaydet</button>
          </div>
        </form>






      </div>
    </div>
  </div>
</div>


@stop

@section('css')


@stop

@section('javascript')



@stop
