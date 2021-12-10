@extends('voyager::master')

@section('page_title', 'Özel Rapor Oluştur')

@section('page_header')
<div class="container-fluid">
  <h1 class="page-title">
    <i class="voyager-archive"></i>Özel Rapor Oluştur
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

          <form role="form" class="form-edit-add" action="{{ isset($report->id ) ? route('voyager.custom-reports.update', $report->id) : route('voyager.custom-reports.store') }}" method="POST" enctype="multipart/form-data">

            @if(isset($report->id ))
              {{ method_field("PUT") }}
              @endif
              @csrf
              <div class="panel-body">
                <div class="form-group  col-md-6">
                  <label class="control-label" for="name">Rapor Adı</label>
                  <input type="text" class="form-control" name="name" placeholder="Rapor Adı" value="{{$report->name ?? ''}}">
                </div>
                <div class="form-group  col-md-3">
                  <label class="control-label" for="name">Sutun</label>
                  <input type="number" class="form-control table" id='col' value="0">
                </div>
                <div class="form-group  col-md-3">
                  <label class="control-label" for="name">Satır</label>
                  <input type="number" class="form-control table" id='row' value="0">
                </div>
                <table id='datas'>

                </table>
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

<div class="modal fade" id="addCellInfo" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4>Veri Ekle</h4>
      </div>
      <div class="content">
          <input type="hidden" id="targetCell" value=""/>
          <div class="col-md-6">
            <label class="control-label">Veri Tipi</label>
          <select  class="form-control select2" id="cellType" >
              <option value='text'>Metin</option>
              <option value='tag'>Etiket</option>
              <option value='date'>Tarih</option>
              <option value='user'>Kullanıcı</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="control-label">Metin</label>
          <input type="text"  class="form-control" id="cellText" value=""/>
      </div>
          <div class="col-md-6">
            <label class="control-label">Cihaz</label>
            <select class="form-control select2" id="device" disabled>
              <option value='null'>Seçin</option>
              @foreach ($devices as $device)
                <option value='{{$device->id}}'>{{$device->name}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="control-label">Etiket</label>
            <select class="form-control select2"  id="deviceTags" disabled>

            </select>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
          <input type="submit" value="Ekle" class="btn btn-primary save" onclick="addtocell()">
        </div>

    </div>
  </div>
</div>



@stop

@section('css')
<style>
#datas tr td span{
  width: 180px;
display: inline-block;  
  font-size: 10px;
}
.panel-body{
  overflow-x: scroll;

}
</style>

@stop

@section('javascript')

  <script type="text/javascript">


    $(document).ready(function() {

      var devices = [];
      @foreach ($devices as $device)
      devices[{{$device->id}}] = {!! $device->tags !!} ;
      @endforeach
      $('#cellType').change(function() {
        if($(this).val() == 'text'){
          $('#device').prop( "disabled", true );
          $('#deviceTags').prop( "disabled", true );
        }
        if($(this).val() == 'tag'){
          $('#device').prop( "disabled", false );
          $('#deviceTags').prop( "disabled", false );
        }

        if($(this).val() == 'user'){
          $('#device').prop( "disabled", true );
          $('#deviceTags').prop( "disabled", true );
          $('#cellText').val('Raporu Oluşturan');
        }

        if($(this).val() == 'date'){
          $('#device').prop( "disabled", true );
          $('#deviceTags').prop( "disabled", true );
          $('#cellText').val('Rapor Tarihi');
        }
      });


      $('#device').change(function() {
        selected = $(this).val();
        obj = devices[selected] ;
        $('#deviceTags').empty();
        $('#deviceTags').append("<option value='null'>Seçin</option>");

        Object.keys(obj).forEach(function(k){
            $('#deviceTags').append("<option value='{\"device\":" + selected + ",\"device_index\":" + k + "}'>" + obj[k] + "</option>");
        });
      });

      $('#deviceTags').change(function() {
        $('#cellText').val($( "#deviceTags option:selected" ).text());
      });
      $('#device').trigger("change");
    });

    function addtocell(){
      var cell = $('#targetCell').val();
      $('#text_'+cell).val($("#cellText").val());
      $('#span_'+cell).text($("#cellText").val());
      $('#value_'+cell).val($("#deviceTags").val());
      $('#type_'+cell).val($("#cellType").val());
      $("#cellText").val("");
      $("#deviceTags").val("");
      $('#addCellInfo').modal('toggle');
    }

function addtomodal(cell){
  $('#targetCell').val(cell);
  $('#cellType').val($('#type_'+cell).val());
  $('#cellText').val($('#text_'+cell).val());
  const obj = JSON.parse($('#value_'+cell).val());
  console.log(obj);
  $('#device').val(obj.devive);
  $('#device').trigger("change");
  $('#deviceTags').val($('#value_'+cell).val());
}



    $('.table').change(function(){
    $('#datas').find('tr').addClass('delete');
    $('#datas').find('tr').find('td').addClass('delete');
    var row = $('#row').val();
    var col = $('#col').val();
    for (i = 1; i <= row; i++) {
      if ($('#tr_'+i).length === 0) {
          $('#datas').append('<tr id="tr_' + i + '"></tr>');
       }
       $('#tr_'+i).removeClass('delete');
       for (ii = 1; ii <= col; ii++) {
         if ($('#td_'+i+'_'+ii).length === 0) {
             $('#tr_'+i).append('<td id="td_'+i+'_'+ii + '"></td>');
             $('#td_'+i+'_'+ii).append('<input type="hidden" value="" name="datas['+i+']['+ii+'][type]" id="type_'+i+'_'+ii + '"/>');
             $('#td_'+i+'_'+ii).append('<input type="hidden" value="" name="datas['+i+']['+ii+'][value]" id="value_'+i+'_'+ii + '"/>');
             $('#td_'+i+'_'+ii).append('<input type="hidden" value="" name="datas['+i+']['+ii+'][text]" id="text_'+i+'_'+ii + '"//>');
             $('#td_'+i+'_'+ii).append('<span id="span_'+i+'_'+ii + '">&nbsp;</span>');
             $('#td_'+i+'_'+ii).append('<button type="button" class="btn btn-primary" id="bt_'+i+'_'+ii + '"  data-toggle="modal" href="#addCellInfo" onclick="addtomodal(\''+i+'_'+ii+'\')">+</button>')
          }
          $('#td_'+i+'_'+ii).removeClass('delete');
       }
    }
    $('.delete').remove();
    });
  $(document).ready(function() {

  @if (isset($report->name ))
    @php
    $datas = json_decode($report->datas, true);
    @endphp
    @foreach ($datas as $row => $cols)
      $('#row').val("{{$row}}");
      $('#datas').append('<tr id="tr_{{$row}}"></tr>');
      @foreach ($cols as $col => $values)
      $('#col').val("{{$col}}");
      $('#tr_{{$row}}').append('<td id="td_{{$row}}_{{$col}}"></td>');
      $('#td_{{$row}}_{{$col}}').append('<input type="hidden" value="{{$values['type']}}" name="datas[{{$row}}][{{$col}}][type]" id="type_{{$row}}_{{$col}}"/>');
      $('#td_{{$row}}_{{$col}}').append('<input type="hidden" value="{{$values['value']}}" name="datas[{{$row}}][{{$col}}][value]" id="value_{{$row}}_{{$col}}"/>');
      $('#td_{{$row}}_{{$col}}').append('<input type="hidden" value="{{$values['text']}}" name="datas[{{$row}}][{{$col}}][text]" id="text_{{$row}}_{{$col}}"//>');
      $('#td_{{$row}}_{{$col}}').append('<span id="span_{{$row}}_{{$col}}">{{$values['text']}}</span>');
      $('#td_{{$row}}_{{$col}}').append('<button type="button" class="btn btn-primary" id="bt_{{$row}}_{{$col}}"  data-toggle="modal" href="#addCellInfo" onclick="$(\'#targetCell\').val(\'{{$row}}_{{$col}}\')">+</button>')
      @endforeach
    @endforeach
    @endif




  });


</script>
@stop
