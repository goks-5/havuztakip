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

<style>
    .dynamic-content {
        margin-right: 5px; /* Slightly reduce spacing */
        display: inline-block;
        vertical-align: middle; /* Align with the info icon */
    }
    
    .info-icon {
        margin-left: 5px; /* Slightly adjust the spacing */
        vertical-align: middle; /* Align with the dynamic content */
        cursor: pointer; /* Show cursor pointer */
    }
    
    .tooltip-content {
        position: absolute;
        background: #f9f9f9;
        border: 1px solid #ccc;
        padding: 8px; /* Slightly adjust padding */
        z-index: 1000;
        width: auto; /* Auto width to adjust based on content */
        max-width: 300px; /* Add a max-width */
        white-space: nowrap; /* Keep content on a single line */
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Optional shadow for better visibility */
        margin-top: 5px; /* Slight space from icon */
        /* Customize tooltip appearance */
    }
    
    .tooltip-content ul {
        padding-left: 15px; /* Add some padding to the list */
        margin: 0; /* Remove default margin */
        list-style-type: disc; /* Use disc style for list */
    }
    
    .tooltip-content li {
        margin-bottom: 5px; /* Space between list items */
    }
    
    .tooltip-content li:last-child {
        margin-bottom: 0; /* Remove space from last item */
    }
    </style>