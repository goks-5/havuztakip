<div class="tool_data row ">
  @include('dashboard.tools.toolSettings',['tool'=>$tool])
    @php
    $setting = json_decode($tool->settings,true);
    @endphp

    <table id="tool_{{$tool->id}}" class="table table-hover">
      <!-- Tablo verilerin buraya gelecek -->
    </table>

</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<!-- Butonlar için yeni bir div ekliyoruz, yatay dizilim sağ alta olacak -->
<div class="btn-group-horizontal" style="position: absolute; right: 10px; bottom: 10px;">
  <a href="#" class="btn btn-primary btn-sm edit" onclick="printDiv('tool_{{$tool->id}}')">
    <i class="fa fa-print"></i> <!-- Print ikonu -->
  </a>
  <a href="#" class="btn btn-success btn-sm" onclick="exportToTablo('tool_{{$tool->id}}')">
    <i class="fa fa-file-excel"></i> <!-- Excel ikonu -->
  </a>
</div>

