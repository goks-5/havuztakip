
@php
  $device = App\Device::find($settings['device']);
  if($device){
    $ismanuel =  $device->mac == '00:00:00:00:00:02';
  }else{
    $ismanuel = false ;
  }
  $farkli_id = $tool->id . "_" .$settings['device_index'];
@endphp

<div class="tool_data row">
  @include('dashboard.tools.toolSettings',['tool'=>$tool])
  @if (!empty($settings['title']))
  <div class="col-xs-5 tool_data_title">{{$settings['title']}} </div>
  @endif
  <div class="@if (!empty($settings['title'])) col-xs-7 @else col-xs-12 @endif">
    <span id="tool_{{$tool->id}}">...</span> {{ $settings['unit']  }}
@if($ismanuel)
  <span type="button" data-toggle="modal" data-target="#manuelVeri_{{$farkli_id}}" style="float:right">
    <i class="voyager-plus" ></i>
  </span>
@endif
  </div>
</div>
@if($ismanuel)
  <div class="modal fade" id="manuelVeri_{{$farkli_id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Manuel Veri Ekle</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Kapat">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="manuelAdd_{{$farkli_id}}"  method="POST" action="{{route('manuelAdd')}}">
            @csrf
            <input type="hidden" name="id" value="{{$settings['device']}}"/>
            <div class="form-group">
              <label for="date">Tarih</label>
              <input type="datetime-local" class="form-control"  name="date" value="{{date("Y-m-d\TH:i")}}"/>
            </div>
              <div class="form-group">
                <input type="number" class="form-control"  name="tags[{{ $settings['device_index']}}]"  step="0.01" value="0"/>
              </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
          <button type="submit" form="manuelAdd_{{$farkli_id}}" value="Kaydet" class="btn btn-primary">Kaydet</button>
        </div>
      </div>
    </div>
  </div>
@endif
