@php
  if($row->field == "reporting_user"){
    $options->default =  Auth::user()->name ;
  }
@endphp
<input @if($row->required == 1) required @endif type="text" class="form-control" id="form-{{ $row->field }}" name="{{ $row->field }}"
        placeholder="{{ old($row->field, $options->placeholder ?? $row->getTranslatedAttribute('display_name')) }}"
       {!! isBreadSlugAutoGenerator($options) !!}
       value="{{ old($row->field, $dataTypeContent->{$row->field} ?? $options->default ?? '') }}" autocomplete="off">
 @push('javascript')
   <script>


$(document).ready(function(){
var item_{{ $row->field }} =[];
 $('#form-{{ $row->field }}').typeahead({
   minLength: 0,
   showHintOnFocus: true,
  source: function(query, result)
  { var  rule = {};
    @foreach ($row->details->autoComplate as $value)
      @if($value != 'company_id')
        rule['{{$value}}']=$("input[name='{{$value}}']").val();
      @endif
    @endforeach
   $.ajax({
    url:"/autocomplate",
    method:"POST",
    data:{query: $("input[name='{{$row->field}}']").val(),field:'{{$row->field}}',id:'{{$row->id}}',rule:rule},
    dataType:"json",
    success:function(data)
    {
     result($.map(data, function(item){
       item_{{ $row->field }}.push(item);
      return item;
     }));
    }
   })
  }
});

@if(@isset($row->details->addrule))
//kural var
  @cannot($row->details->addrule,app('App\Fault'))
    $('#form-{{ $row->field }}').focusout(function(){
        if(!item_{{ $row->field }}.includes($('#form-{{ $row->field }}').val()) && $('#form-{{ $row->field }}').val().length > 0){
          alert("Yeni {{$row->getTranslatedAttribute('display_name')}} ekleme yetkiniz yok lütfen listeden seçin!!");
          $('#form-{{ $row->field }}').val("");
        }
    });
  @endcannot
@endif



});
</script>

@endpush
