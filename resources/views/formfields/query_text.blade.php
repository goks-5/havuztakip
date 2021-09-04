<div class="form-group" style="display:flex">
<input type="text" class="form-control" id="form-{{ $row->field }}" name="{{ $row->field }}" value="{{ old($row->field, $dataTypeContent->{$row->field} ?? $options->default ?? '') }}"
style="pointer-events:none;background:#ddd;"
>
<i class="voyager-pen" id='edit_{{ $row->field }}' style="
    padding-top: 3px;
    min-width: 35px;
    text-align: center;
    font-size: 20px;"></i>
</div>


@push('javascript')
<script>
  $(document).ready(function() {
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.getAll('{{ $row->field }}').length > 0) {
      $('#form-{{ $row->field }}').val(urlParams.getAll('{{ $row->field }}'));
    }

    $('#edit_{{ $row->field }}').on('click', function() {
      if($('#form-{{ $row->field }}').css('pointer-events') === 'none'){
        $('#form-{{ $row->field }}').css("background-color", "#fff");
        $('#form-{{ $row->field }}').css("pointer-events", "auto");
      }else{
        $('#form-{{ $row->field }}').css("background-color", "#ddd");
        $('#form-{{ $row->field }}').css("pointer-events", "none");
      }

    })


  });
</script>
@endpush
