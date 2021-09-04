<div class="tool_data row">
  @include('dashboard.tools.toolSettings',['tool'=>$tool])
    @php
    $barcodes = App\GroupTag::where('company_id', Auth::user()->company_id)->get();

    @endphp

<div class="table-responsive">
  <table id="tagstable" class="table table-hover">
  <thead>
    <tr>
<th>Etiket</th>
<th>Cihaz</th>
<th>Değer</th>
</tr>
</thead>
</table>
</div>



    <div class="col-xs-12 tool_data_title" style="text-align: right;">
      <a  href="#confirm-start" data-toggle="modal" data-target="#confirm-start" class="btn btn-primary btn-sm"><span class="icon voyager-tag"></span> <span class="title">Yeni Etiket Ekle</span></a>
    </div>
</div>

<div class="modal fade" id="confirm-start" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Yeni Etiket Ekleyin</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
        <div class="modal-body">
        <form method="post" action="{{route('TagsAdd')}}" id="TagsAdd">
          {{ csrf_field() }}
          <input type="hidden" name="tags_group" value=""  id="tags_group" required/>
          <div class="form-group row">
            <div class="col-xs-6">
              <label class="control-label">Cihazlar</label>
              <select class="form-control" name="data_tags" id="data_tags" required>
                  <option value=""> Seçim Yapın</option>
                @foreach ($barcodes as $barcode)
                @php
                $tags = json_decode($barcode->data_tags,true);
                $value = '[';
                @endphp
                @foreach ($tags as $tag)
                  @php
                  $tagdetail = explode("-",$tag);
                  $value .= '{"device":'.$tagdetail[0] .',"device_index":'.$tagdetail[1] .'},';
                  @endphp
                @endforeach
                @php
                $value =  trim($value,",");
                $value .= ']';
                @endphp
                  <option value='{{$value}}' data-group="{{$barcode->id}}">{{$barcode->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="col-xs-6">
              <label class="control-label">Etiket</label>
              <input type="text" name="name" class="form-control" id="tagName" required/>
            </div>
          </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
        <input type="submit" value="Kaydet" class="btn btn-primary save" />
      </div>
      </form>
    </div>
  </div>
</div>
</div>


<div class="modal fade" id="confirm-end" tabindex="-1" role="dialog" aria-labelledby="confirm-end" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <h4>Etiketi Bitir</h4>
            </div>
            <div class="modal-body" id="end_tag_body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Vazgeç</button>
                <a class="btn btn-danger btn-ok" id="end_tag">Etiketi Bitir</a>
            </div>
        </div>
    </div>
</div>





 @push('javascript')

	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowgroup/1.1.1/css/rowGroup.dataTables.min.css">
   <script type="text/javascript" language="javascript" src="/js/dataTables.rowGroup.min.js"></script>
   <script src="/js/jquery.validate.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {

  $('#data_tags').on('change', function() {
      $('#tags_group').val($(this).find(':selected').data('group'));
  });
  $('#confirm-end').on('show.bs.modal', function(e) {
      $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
  });

     $('#tagstable').DataTable({searching: false, paging: false, info: false,rowGroup: {
            dataSrc: 0
        },
        columnDefs: [ {
            targets: [ 0 ],
            visible: false
        } ]});
     });
</script>
<script type="text/javascript">

var allDevice = [];
  @foreach ($barcodes as $barcode)
    allDevice[{{$barcode->id}}] = '{{$barcode->barcode}}';
  @endforeach
var activeDevice = [];


var BarcodesScanner = {
    barcodeData: '',
    deviceId: '',
    symbology: '',
    timestamp: 0,
    dataLength: 0
};

function onScannerNavigate(barcodeData, deviceId, symbology, timestamp, dataLength){
    BarcodesScanner.barcodeData = barcodeData;
    BarcodesScanner.deviceId = deviceId;
    BarcodesScanner.symbology = symbology;
    BarcodesScanner.timestamp = timestamp;
    BarcodesScanner.dataLength = dataLength;
    $(BarcodesScanner).trigger('scan');
}


BarcodesScanner.tmpTimestamp = 0;
BarcodesScanner.tmpData = '';
$(document).on('keypress', function(e){
    e.stopPropagation();
    var keycode = (e.keyCode ? e.keyCode : e.which);
    if (BarcodesScanner.tmpTimestamp < Date.now() - 500){
        BarcodesScanner.tmpData = '';
        BarcodesScanner.tmpTimestamp = Date.now();
    }
    if (keycode == 13 && BarcodesScanner.tmpData.length > 0){
        onScannerNavigate(BarcodesScanner.tmpData, 'FAKE_SCANNER', 'WEDGE', BarcodesScanner.tmpTimestamp, BarcodesScanner.tmpData.length);
        BarcodesScanner.tmpTimestamp = 0;
        BarcodesScanner.tmpData = '';
    } else if (e.charCode && e.charCode > 0) {
        BarcodesScanner.tmpData += String.fromCharCode(e.charCode);
    }
});
$(BarcodesScanner).on('scan', function(e){
  console.log(BarcodesScanner.barcodeData);
  if(allDevice.includes(BarcodesScanner.barcodeData)){
    if(activeDevice.includes(BarcodesScanner.barcodeData)){
    $('#end_tag').attr('href', '{{route('end_tag')}}?id='+  activeDevice.indexOf(BarcodesScanner.barcodeData));
    $('#end_tag_body').text( BarcodesScanner.barcodeData + " Barkod kodlu Makina etiketini sonlandır");
      $('#confirm-start').modal('hide');
      $('#confirm-end').modal('show');}
      else{
        $("#data_tags option:selected").attr("selected", false)
        $("#data_tags option[data-group="+allDevice.indexOf(BarcodesScanner.barcodeData)+"]").attr('selected', 'selected');
        $("#data_tags").trigger('change');
        $('#confirm-end').modal('hide');
        $('#confirm-start').modal('show');
    }
  }else{
    if(($('#confirm-start').data('bs.modal') || {}).isShown  ){
      if(BarcodesScanner.barcodeData == "ONAYLA"){
        $("#TagsAdd").validate();
      if ($('#TagsAdd').valid())
        $("#TagsAdd").submit();
      }else{
        $("#tagName").val(BarcodesScanner.barcodeData);
      }
    }else if(($('#confirm-end').data('bs.modal') || {}).isShown ){
      if(BarcodesScanner.barcodeData == "BITIR"){
          window.location = $('#end_tag').attr("href");
      }
    }
    if(BarcodesScanner.barcodeData == "IPTAL"){

        $("#data_tags option:selected").attr("selected", false);
        $("#data_tags").trigger('change');
        $("#tagName").val("");
        $('#end_tag').attr('href', '#');
        $('#end_tag_body').text(" ");
        $('.modal').modal('hide');
    }
  }
});
</script>

@endpush
