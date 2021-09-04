
  <input type="hidden" id="form-hidden-{{ $row->field }}" name="{{ $row->field }}" value="{{ old($row->field, $dataTypeContent->{$row->field} ?? $options->default ?? '') }}" />
  <input type="text" class="form-control col-sm-11" id="form-{{ $row->field }}" name="{{ $row->field }}" value="{{ old($row->field, $dataTypeContent->{$row->field} ?? $options->default ?? '') }}" disabled>
 <i class="voyager-refresh col-sm-1" id='edit_{{ $row->field }}' style="
 padding-top: 3px;
 min-width: 35px;
 text-align: right;
 font-size: 21px;
 position: absolute;
 right: 10px;
 top: 25px;"></i>

@push('javascript')
<script>
  $(document).ready(function() {


    $('#edit_{{ $row->field }}').on('click', function() {
      var result           = '';
         var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
         var charactersLength = characters.length;
         for ( var i = 0; i < 35; ++i ) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
         }
         $('#form-{{ $row->field }}').prop( "disabled", false );
         $('#form-hidden-{{ $row->field }}').prop( "disabled", true );
         $('#form-hidden-{{ $row->field }}').val(result);
         $('#form-{{ $row->field }}').val(result);
    })
    @if(old($row->field, $dataTypeContent->{$row->field} ?? $options->default ?? '') == "")
      $('#edit_{{ $row->field }}').trigger( "click" );
    @endif

  });
</script>
@endpush
