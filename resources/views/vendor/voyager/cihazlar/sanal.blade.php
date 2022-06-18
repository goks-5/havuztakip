@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' Sanal Cihaz Ekle')

@section('page_header')
<div class="container-fluid">
  <h1 class="page-title">
    <i class="voyager-bar-chart"></i> Sanal Cihaz Ekle
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

          <form role="form" class="form-edit-add" action="{{route('sanalkaydet')}}" method="POST" enctype="multipart/form-data">
  @csrf

            <div class="panel-body">



@if(isset($device->id ))
<input type="hidden" name='id' value="{{$device->id}}"/>
@endif

              <div class="form-group  col-md-12 ">
                <label class="control-label" for="name">Takma Ad</label>
                <input type="text" class="form-control" name="name" placeholder="Takma Ad" value="{{ isset($device->name ) ? $device->name  : '' }}">
              </div>


              <div class="form-group  col-md-12 ">
                <label class="control-label" for="name">Etiket</label>
                <input type="hidden" class="form-control" id="form-tags" name="tags" value="{{ isset($device->tags ) ? $device->tags  : '' }}">
                <input type="hidden" class="form-control" id="form-formula" name="formula" value="{{ isset($device->formula ) ? $device->formula  : '' }}">
                <input type="hidden" class="form-control" id="form-type" name="type" value="{{ isset($device->type ) ? $device->type  : '' }}">
                <div class='con_tags'>
                  <div class="form-group mtextrow">

                    <div class="row">
                      <div class="col-sm-4">
                      <input type="text" data-name="" data-index="0" class="form-control multiple_tags" name="__tags[0]" placeholder="0. Etiket" id="tags_0">
                      </div>
                    <div class="col-sm-4">
                    <select class="form-control type"  name="__type[0]" id="type_0">
                        <option value='diff'>Fark Değer</option>
                        <option value='last'>Son Değer</option>
                        <option value='first'>İlk Değer</option>
                        <option value='avg'>Ortalama Değer</option>
                        <option value='max'>En Büyük Değer</option>
                        <option value='min'>En Küçük Değer</option>
                        <option value='triger[0]'>0:00 da tetiklen</option>
                        <option value='triger[1]'>1:00 da tetiklen</option>
                        <option value='triger[2]'>2:00 da tetiklen</option>
                        <option value='triger[3]'>3:00 da tetiklen</option>
                        <option value='triger[4]'>4:00 da tetiklen</option>
                        <option value='triger[5]'>5:00 da tetiklen</option>
                        <option value='triger[6]'>6:00 da tetiklen</option>
                        <option value='triger[7]'>7:00 da tetiklen</option>
                        <option value='triger[8]'>8:00 da tetiklen</option>
                        <option value='triger[9]'>9:00 da tetiklen</option>
                        <option value='triger[10]'>10:00 da tetiklen</option>
                        <option value='triger[11]'>11:00 da tetiklen</option>
                        <option value='triger[12]'>12:00 da tetiklen</option>
                        <option value='triger[13]'>13:00 da tetiklen</option>
                        <option value='triger[14]'>14:00 da tetiklen</option>
                        <option value='triger[15]'>15:00 da tetiklen</option>
                        <option value='triger[16]'>16:00 da tetiklen</option>
                        <option value='triger[17]'>17:00 da tetiklen</option>
                        <option value='triger[18]'>18:00 da tetiklen</option>
                        <option value='triger[19]'>19:00 da tetiklen</option>
                        <option value='triger[20]'>20:00 da tetiklen</option>
                        <option value='triger[21]'>21:00 da tetiklen</option>
                        <option value='triger[22]'>22:00 da tetiklen</option>
                        <option value='triger[23]'>23:00 da tetiklen</option>
                        
                    </select>
                  </div>
                  <div class="col-sm-1">
                  <div class="form-check">
                  <input class="form-check-input multiple_tags" type="checkbox" data-index="100" data-name=" Saatlik" value="Sayaç 1 Saatlik" id="tags_100" name="__tags[100]">
                  <label class="form-check-label" for="tags_100">Saatlik</label>
                  </div>
                  </div>
                  <div class="col-sm-1">
                  <div class="form-check">
                  <input class="form-check-input multiple_tags" type="checkbox" data-index="200" data-name=" Günlük" value="Sayaç 1 Günlük" id="tags_200" name="__tags[200]">
                  <label class="form-check-label" for="tags_200">Günlük</label>
                  </div>
                  </div>
                    <div class="col-sm-1">
                    <div class="form-check">
                    <input class="form-check-input multiple_tags" type="checkbox" data-index="300" data-name=" Haftalık" value="Sayaç 1 Haftalık" id="tags_300" name="__tags[300]">
                    <label class="form-check-label" for="tags_300">Haftalık</label>
                    </div>
                    </div>
                    <div class="col-sm-1">
                    <div class="form-check">
                    <input class="form-check-input multiple_tags" type="checkbox" data-index="400" data-name=" Aylık" value="Sayaç 1 Aylık" id="tags_400" name="__tags[400]">
                    <label class="form-check-label" for="tags_400">Aylık</label>
                    </div>
                    </div>
                    </div>
                        <div class="row">
  <div class="col-sm-6">
    <input type="text" class="form-control formul"  name="__formuls[]" placeholder="Formül">
    <span class='formul_response' ></span>
  </div>
        <div class="col-sm-6">
          <select class="selectpicker islem" size="10">
            @foreach ($devices as $device)
            @php
            $tags = json_decode($device->tags,true);
            @endphp
            <optgroup label="{{$device->name}}">
              @foreach ($tags as $key => $tag)
              <option value='[{{$device->id}}_{{$key}}]'>{{$tag}}</option>
              @endforeach
            </optgroup>
            @endforeach
          </select>
        </div>

                    </div>

                  </div>

                </div>
                <div class="form-group">
                  <input type="button" class="btn btn-warning" value='Yeni Etiket Ekle' id='add_tags' />
                  <input type="button" class="btn btn-danger" value='Son Etiket Sil' id='remove_tags' />
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
<style>
  .formul_response{
    font-weight: bolder;
    padding: 12px;
    font-size: large;
  }

</style>

@stop

@section('javascript')


<script>



  $(document).ready(function() {


   $(document).on('dblclick', ".islem", function () {

$(this).closest('div').prev().find('input').val($(this).closest('div').prev().find('input').val() + $(this).val());

$(this).closest('div').prev().find('input').trigger( "input" );
  $('#form-formula').val(JSON.stringify($('.formul').serializeJSON().__formuls));

})

    var $row = $('.mtextrow');

    var kayitli = JSON.parse('[]');
    if ($('#form-tags').val().length > 0) {
      kayitli = JSON.parse($('#form-tags').val());
    }
    var kayitli2 = JSON.parse('[]');
    if ($('#form-formula').val().length > 0) {
      kayitli2 = JSON.parse($('#form-formula').val());
    }
    var kayitlitype = JSON.parse('[]');
    if ($('#form-type').val().length > 0) {
      kayitlitype = JSON.parse($('#form-type').val());
    }

      for (elem in kayitli) {
        write_tags(kayitli[elem],elem);
      }


    function write_tags(item, index) {
if( index < 100){
      if (index != 0) {
        $row.clone().insertAfter('.mtextrow:last');
      }
      var $currentRow = $('.mtextrow:last');
      $currentRow.find('.multiple_tags').each(function() {
        this.value = item + $(this).data('name');
        var yindex = (index * 1) + ($(this).data('index') * 1);
        this.name = "__tags["+ yindex +"]";
        this.id = "tags_"+ yindex ;
        if(yindex >= 100){
            $(this).prop( "checked", false );
            if( typeof kayitli[yindex] !== 'undefined'){
              $(this).prop( "checked", true );
            }
        }else{
        this.placeholder = $currentRow.index() + '. Etiket';

        }
      });


      $currentRow.find('.type').each(function() {
        $(this).val(kayitlitype[index]);
        this.name = "__type["+ $currentRow.index()  +"]";
        this.id = "type_"+ $currentRow.index()  ;

      });
      $currentRow.find('.formul').each(function() {

        this.value = kayitli2[index];
      });

    }
  }

  $('.type').on('change', function() {
  $('#form-type').val(JSON.stringify($('.type').serializeJSON().__type));
  });
    $('.con_tags').on('input', function() {
      $('.con_tags').each(function() {

         $(this).find('input').each(function() {
           if($(this).data('name') == ""){
           value = this.value ;
         }
           if( typeof $(this).data('name') !== 'undefined'){
             this.value = value + $(this).data('name');
           }
         });
      })


      $('#form-tags').val(JSON.stringify($('.multiple_tags').serializeJSON().__tags));
        $('#form-formula').val(JSON.stringify($('.formul').serializeJSON().__formuls));
    });


    $('#add_tags').on('click', function() {
      var $newRow = $row.clone().insertAfter('.mtextrow:last');
      var $currentRow = $('.mtextrow:last');
      $newRow.find('.multiple_tags').each(function() {
        this.value = '';
        var yindex = ($currentRow.index() * 1) + ($(this).data('index') * 1);
        this.name = "__tags["+ yindex +"]";
        this.placeholder = $currentRow.index() + '. Etiket';
      });
      $newRow.find('.formul').each(function() {
        this.value = '';
      });
      $newRow.find('.type').each(function() {
      this.name = "__type["+ $currentRow.index()  +"]";
          this.id = "type_"+ $currentRow.index()  ;
      });
    });

    $('#remove_tags').on('click', function() {

      var $currentRow = $('.mtextrow:last');
      if ($currentRow.index() === 0) {
        alert("İlk Satır Silinmez");
      } else {
        $currentRow.remove();
        $('#form-tags').val(JSON.stringify($('.multiple_tags').serializeJSON().__tags));
        $('#form-formula').val(JSON.stringify($('.formul').serializeJSON().__formuls));
        $('#form-type').val(JSON.stringify($('.type').serializeJSON().__type));
      }
    });

    $('.formul').on('input', function() {

var responseSpan = $(this).nextAll('.formul_response:first');

     $(this).val( this.value.replace(',','.'));
      $.ajax({
      url: '{{route('calculate')}}',
      type: 'post',
      data: {tag:this.value,_token :'{{csrf_token()}}'},
      success: function(result) {
                responseSpan.html('Şimdiki sonuç : ' + result);
            }
      });

    });

  });
</script>

@stop
