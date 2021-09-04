<input type="hidden" class="form-control" id="form-settings" name="settings" value="{{ old($row->field, $dataTypeContent->{$row->field} ?? $options->default ?? '') }}">

@include('formfields.type.device_data')
@include('formfields.type.device_data_gauge')
@push('javascript')
<script>
  $(document).ready(function() {
    var tanimli = JSON.parse($('#form-settings').val());

    $('.form-edit-add').submit(function() {
      switch ($('input[name="type"]:checked').val()) {
        case 'device_data':
          cikti = {
            device: $('select[name="device_data[device]"]').val(),
            device_index: $('select[name="device_data[device_index]"]').val(),
            unit: $('input[name="device_data[unit]"]').val(),
            show_title: $('input[name="device_data[show_title]"]').is(':checked'),
            css: {
              color: $('input[name="device_data[color]"]').val(),
              'font-size': $('select[name="device_data[font-size]"]').val()
            }
          };
          $('#form-settings').val(JSON.stringify(cikti));
          break;
          case 'device_data_gauge':
            cikti = {
              device: $('select[name="device_data_gauge[device]"]').val(),
              device_index: $('select[name="device_data_gauge[device_index]"]').val(),
              unit: $('input[name="device_data_gauge[unit]"]').val(),
              max: $('input[name="device_data_gauge[max]"]').val(),
              min: $('input[name="device_data_gauge[min]"]').val(),
              redFrom: $('input[name="device_data_gauge[redFrom]"]').val(),
              redTo: $('input[name="device_data_gauge[redTo]"]').val(),
              yellowFrom: $('input[name="device_data_gauge[yellowFrom]"]').val(),
              yellowTo: $('input[name="device_data_gauge[yellowTo]"]').val(),
              greenFrom: $('input[name="device_data_gauge[greenFrom]"]').val(),
              greenTo: $('input[name="device_data_gauge[greenTo]"]').val()
            };
            $('#form-settings').val(JSON.stringify(cikti));
            break;
      }

    });

    $.ajax({
      url: '{{route('devicelist')}}',
      success: function(data) {
        var devices = JSON.parse(data);
        $.each(devices, function(key, value) {
          if (value.id == tanimli.device) {
            $('.select_device').append("<option value='" + value.id + "' data-tags='" + JSON.stringify(value.tags) + "' selected>" + value.name + "</option>");
          } else {
            $('.select_device').append("<option value='" + value.id + "' data-tags='" + JSON.stringify(value.tags) + "'>" + value.name + "</option>");

          }
        });

        var tags = JSON.parse(JSON.parse($('select[name="device_data[device]"]').find(':selected').data('tags')));
        $.each(tags, function(key, value) {
          if (key == tanimli.device_index) {
            $('.select_device_index').append('<option value="' + key + '"  selected >' + value + '</option>');
          } else {
            $('.select_device_index').append('<option value="' + key + '"   >' + value + '</option>');

          }

        });

      }
    });

    $('#' + $('input[name="type"]:checked').val()).show();
    $('input[name="type"]').on('change', function() {
      $('.dashboardtype').hide();
      $('#' + $('input[name="type"]:checked').val()).show();
    });
  });
</script>
@endpush
