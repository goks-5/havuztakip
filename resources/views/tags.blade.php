@php
$devices = App\Device::where('company_id', Auth::user()->company_id)->get();
  $selected= json_decode($content,true);
@endphp

<select class="form-control selectpicker" name="{{ $row->field }}[]"  multiple @if (isset($dataTypeContent[0])) disabled @endif >
  @foreach ($devices as $device)
  @php
  $tags = json_decode($device->tags,true);
  @endphp
  <optgroup label="{{$device->name}}">
    @foreach ($tags as $key => $tag)
    <option value='{{$device->id}}-{{$key}}' @if (in_array($device->id.'-'.$key,(array)$selected)) selected @endif >{{$tag}}</option>
    @endforeach
  </optgroup>
  @endforeach
</select>

@push('javascript')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.5.4/bootstrap-select.css">
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.5.4/bootstrap-select.js"></script>
<script type="text/javascript">

$(document).ready(function() {
  $('.selectpicker').selectpicker({
    noneSelectedText: 'Seçim Yapmalısınız'
  });
});
</script>
@endpush
