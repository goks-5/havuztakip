<div class="tool_data row">
  @include('dashboard.tools.toolSettings', ['tool' => $tool])
  @php
      $setting = json_decode($tool->settings, true);
  @endphp

  <div id="content_{{$tool->id}}">
      <!-- New div for dynamic content -->
      <div id="tool_{{$tool->id}}" class="dynamic-content">
          <!-- This content will be filled dynamically -->
      </div>

      <!-- "i" icon with tooltip -->
      <i class="glyphicon glyphicon-info-sign info-icon" id="info_{{$tool->id}}" title="Tüm Etiketler"></i>

      <!-- Tooltip div -->
      <div id="tool_{{$tool->id}}_tips" class="tooltip-content" style="display:none;">
          <!-- This content will be filled dynamically -->
      </div>
  </div>
</div>

@push('javascript')
<script>
$(document).ready(function () {
    // On clicking the info icon
    $('#info_{{$tool->id}}').on('click', function (e) {
        e.preventDefault();

        // Get the tooltip div
        var $tooltip = $('#tool_{{$tool->id}}_tips');

        // Toggle tooltip visibility
        if ($tooltip.is(':visible')) {
            $tooltip.hide();
        } else {
            $tooltip.show();
        }
    });

});
</script>
@endpush

<!-- Optional CSS for tooltip appearance -->
<style>
.dynamic-content {
    margin-right: 10px; /* Adjust spacing as needed */
    display: inline-block;
    /* Additional styling for the dynamic content div */
}

.tooltip-content {
    position: absolute;
    background: #f9f9f9;
    border: 1px solid #ccc;
    padding: 10px;
    z-index: 1000;
    width: 200px; /* Adjust width as needed */
    /* Customize tooltip appearance */
}
</style>
