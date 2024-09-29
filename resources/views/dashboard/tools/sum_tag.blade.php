<div class="tool_data row">
    @include('dashboard.tools.toolSettings', ['tool' => $tool])
    @php
        $setting = json_decode($tool->settings, true);
    @endphp
  
    <div id="content_{{$tool->id}}" >
        <!-- New div for dynamic content -->
        <div id="tool_{{$tool->id}}" class="dynamic-content">
            <!-- This content will be filled dynamically -->
        </div>
  
        <!-- "i" icon with tooltip -->
        <i class="glyphicon glyphicon-info-sign info-icon" id="info_{{$tool->id}}" title="Tüm Etiketler" style="cursor: pointer;"></i>
  
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
        margin-right: 5px;
        display: inline-block;
        vertical-align: middle;
    }
    
    .info-icon {
        margin-left: 5px;
        vertical-align: middle;
        cursor: pointer;
    }
    
    .tooltip-content {
        position: absolute;
        background: #f9f9f9;
        border: 1px solid #ccc;
        padding: 8px;
        z-index: 1000;
        width: auto;
        max-width: 300px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 5px;
        text-align: left;
    }
    
    .tooltip-content .tooltip-row {
        display: flex; /* Align label and value in one row */
        justify-content: space-between; /* Space between label and value */
        margin-bottom: 5px; /* Space between rows */
        white-space: nowrap; /* Prevent line break */
    }
    
    .tooltip-content .tooltip-label {
        font-weight: bold;
        margin-right: 10px; /* Space between label and value */
    }
    
    .tooltip-content .tooltip-value {
        margin-left: auto; /* Push value to the right */
    }
    </style>