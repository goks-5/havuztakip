@extends('voyager::master')

@section('page_title', ' Rapor Oluştur')

@section('page_header')
<div class="container-fluid">
  <h1 class="page-title">
    <i class="voyager-archive"></i> Rapor Oluştur
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

          <form role="form" class="form-edit-add" action="{{ isset($report->id ) ? route('voyager.raporlar.update', $report->id) : route('voyager.raporlar.store') }}" method="POST" enctype="multipart/form-data">

            @if(isset($report->id ))
              {{ method_field("PUT") }}
              @endif
              @csrf
              <div class="panel-body">
                <div class="form-group  col-md-2 ">
                  <label class="control-label" for="name">Rapor Adı</label>
                  <input type="text" class="form-control" name="name" placeholder="Rapor Adı" value="{{$report->name ?? ''}}">
                </div>
                <div class="form-group  col-md-3 ">
                  <label class="control-label" for="name">Dönem</label>
                  <div>
                    <div class="custom-control custom-radio custom-control-inline col-md-4">
                      <input type="radio" id="daily" name="period" class="custom-control-input" value="1">
                      <label class="custom-control-label" for="daily">Günlük</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline col-md-4">
                      <input type="radio" id="weekly" name="period" class="custom-control-input"  value="2">
                      <label class="custom-control-label" for="weekly">Haftalık</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline col-md-4">
                      <input type="radio" id="monthly" name="period" class="custom-control-input"  value="3">
                      <label class="custom-control-label" for="monthly">Aylık</label>
                    </div>
                  </div>
                </div>
                <div class="form-group  col-md-2 ">
                  <label class="control-label" for="name">Veri adedi</label>
                  <input type="number" max="31" min="1" class="form-control" name="lenght" placeholder="Rapor Adı" value="{{$report->lenght ?? '1'}}">
                </div>
                <div class="form-group  col-md-2">
                  <label class="control-label" for="name">Yerleşim</label>
                  <div>
                    <div class="custom-control custom-radio custom-control-inline col-md-6">
                      <input type="radio" id="sutun" name="type" class="custom-control-input" value="1">
                      <label class="custom-control-label" for="sutun">Veriler Sutunlarda</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline col-md-6">
                      <input type="radio" id="satir" name="type" class="custom-control-input" value="2">
                      <label class="custom-control-label" for="satir">Veriler Satırlarda</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline col-md-6">
                      <input type="radio" id="sutun" name="type" class="custom-control-input" value="3">
                      <label class="custom-control-label" for="sutun">Veriler Farklarla Sutunlarda</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline col-md-6">
                      <input type="radio" id="satir" name="type" class="custom-control-input" value="4">
                      <label class="custom-control-label" for="satir">Veriler Farklarla Satırlarda</label>
                    </div>
                  </div>
                </div>
                <div class="form-group  col-md-3">
                  <label class="control-label" for="name">Sıralama</label>
                  <div>
                    <div class="custom-control custom-radio custom-control-inline col-md-6">
                      <input type="radio" id="desc" name="order_direction" class="custom-control-input" value="desc">
                      <label class="custom-control-label" for="desc">Yeni tarihler önce</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline col-md-6">
                      <input type="radio" id="asc" name="order_direction" class="custom-control-input" value="asc" >
                      <label class="custom-control-label" for="asc">Eski tarihler önce</label>
                    </div>
                  </div>
                </div>

                <div class="form-group  col-sm-6 ">
                  <label class="control-label" for="name">Seçili Etiketler</label>
                  <div class="droptags">

                  </div>
                </div>
                <div class="form-group col-sm-6">
                  <label class="control-label" for="name">Etiketleri Seçin</label>
                  <select class="form-control islem" size="14">
                    @foreach ($devices as $device)
                    @php
                    $tags = json_decode($device->tags,true);
                    @endphp
                    <optgroup label="{{$device->name}}" >
                      @foreach ($tags as $key => $tag)
                      @if($key > 99)
                       <option value='{{$device->id}}_{{$key}}' class="@if($key < 300) daily @elseif($key < 400) weekly @else monthly @endif " >{{$tag}}</option>
                        @endif

                        @endforeach
                    </optgroup>
                    @endforeach
                  </select>
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
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
  .droptags div {
    font-size: 1.2rem;
    padding: 4px 11px !important;
    margin: 5px 0px !important;
  }

  .droptags {
    padding: 10px;
    height: 293px;
    background: #efefef;
    OVERFLOW-X: overlay;
  }

  .glyphicon {
    color: red;
    float: right;
    font-size: 25px;
  }
</style>

@stop

@section('javascript')

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
  $(document).ready(function() {

$('.islem optgroup , .islem option').hide();
$("input:radio[name='period'][value='{{$report->period ?? '1'}}']").trigger('click');
$("input:radio[name='type'][value='{{$report->type ?? '1'}}']").trigger('click');
$("input:radio[name='order_direction'][value='{{$report->order_direction ?? 'desc'}}']").trigger('click');
    @if (isset($report->name ))

    @php
  $tags_gelen = json_decode($report->tags, true);
  $titles = json_decode($report->titles, true);
    @endphp
    @foreach ($tags_gelen as $key => $value)
    $(".islem option[value='{{$value}}']").each(function() {
      $(this).text("{{$titles[$key] ?? 'bulunamıyor' }}");
    });
    $(".islem").val("{{$value}}").trigger('dblclick');

    @endforeach
    @endif




  });


  $(document).on('change', "input:radio[name='period']", function() {
    $('.ui-sortable-handle').remove();
  $('.islem optgroup , .islem option').hide();

    switch($(this).val()) {
    case "1":
    $('.daily').parent().show();
      $('.daily').show();
      break;
    case "2":
    $('.weekly').parent().show();
      $('.weekly').show();
      break;
    case "3":
    $('.monthly').parent().show();
      $('.monthly').show();
      break;
  }

  });



  $(document).on('dblclick', ".islem", function() {

    if ($(this).val() && $('option:selected', this).is(":visible")) {


      $(".droptags").append('<div data-value="' + $(this).val() + '" class="col-sm-12 ui-state-default"><input class="col-sm-9" name="titles[]" value="' +
        $('option:selected', this).text() + '"/> <input type="hidden" value="' +
        $(this).val() + '" name="tags[]"/><span class="glyphicon glyphicon-remove-circle"></span></div>');

      $(".islem option[value='" + $(this).val() + "']").each(function() {
        $(this).hide();
      });
    }


  });

  $(document).on('click', ".glyphicon", function() {
    $(".islem option[value='" + $(this).parent().data('value') + "']").each(function() {
      $(this).show();
    });
    $(this).parent().remove();
  });

  $(function() {
    $(".droptags").sortable({
      connectWith: ".droptags"
    }).disableSelection();
  });
</script>
@stop
