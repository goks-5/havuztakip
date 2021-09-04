@extends('voyager::master')

@section('content')
<div class="page-content">
  @include('voyager::alerts')
  @include('voyager::dimmers')
  <div class="padding-top">
    <div class="container-fluid">
      @foreach ($rows as $row)
      @include('dashboard.rows.'.$row->row_type,['row'=>$row])
        @endforeach
        <div class="row">
          <div class="col-xs-12 col-sm-12  col-md-12 col-lg-12 mb-o">
            <div class="addRow">
              <a href="#" class='ajaxmodal' data-action_type='add_row' title="Yeni Pencere Grubu Ekle"><i class="voyager-plus"></i></a>
            </div>
          </div>
        </div>

    </div>
  </div>
</div>
@include('widget.ajaxModal')


<div class="modal fade" id="toolTypeModal" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4>Araç Tipi Seçin</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div>
        <ul class="row">
          <li class="deviceTypeList device_data ajaxmodal addtype col-xs-3" data-dismiss="modal" data-type="device_data" data-action_type="add_tool">
          </li>
          <li class="deviceTypeList device_data_gauge ajaxmodal addtype col-xs-3" data-dismiss="modal" data-type="device_data_gauge" data-action_type="add_tool">
          </li>
          <li class="deviceTypeList device_indicator ajaxmodal addtype col-xs-3" data-dismiss="modal" data-type="device_indicator" data-action_type="add_tool">
          </li>
          <li class="deviceTypeList device_chart ajaxmodal addtype col-xs-3" data-dismiss="modal" data-type="device_chart" data-action_type="add_tool">
          </li>
          <li class="deviceTypeList faults ajaxmodal addtype col-xs-3" data-dismiss="modal" data-type="faults" data-action_type="add_tool">
          </li>
          <li class="deviceTypeList tags ajaxmodal addtype col-xs-3" data-dismiss="modal" data-type="tags" data-action_type="add_tool">
          </li>

        <li class="deviceTypeList device_alarm ajaxmodal addtype col-xs-3" data-dismiss="modal" data-type="device_alarm" data-action_type="add_tool">
        </li>
        <li class="deviceTypeList period ajaxmodal addtype col-xs-3" data-dismiss="modal" data-type="period" data-action_type="add_tool">
        </li>
        <li class="deviceTypeList backgroud ajaxmodal addtype col-xs-3" data-dismiss="modal" data-type="backgroud" data-action_type="add_tool">
        </li>
 
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteModal" role="dialog">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4>Silmek İstediğinize Eminmisiniz</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="content">
        <form method="post" action="{{route('dahboardTool')}}">
          {{ csrf_field() }}
          <input type="hidden" name="tool" />
          <input type="hidden" name="row" />
          <input type="hidden" name="action_type" />
          <div class="form-group row">

            <button type="submit" class="btn btn-danger col-xs-4" style="margin: 0 5%;">Sil</button>
            <button type="button" class="btn btn-default col-xs-4" style="margin: 0 5%;" data-dismiss="modal">Vazgeç</button>

          </div>
        </form>


      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>

@stop

@section('css')
<link href="https://fonts.googleapis.com/css?family=Quantico&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.5.4/bootstrap-select.css">


@stop


@section('javascript')

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.5.4/bootstrap-select.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  google.charts.load('current', {
    'packages': ['gauge', 'line', 'corechart','bar'],
    'language': 'tr'
  });
</script>
@include('dashboard.tools.js')
<script type="text/javascript">
  $(document).ready(function() {
    $('.ajaxmodal').click(function() {
      var pdata = $(this).data();
      pdata._token = "{{csrf_token()}}";
      $.ajax({
        url: '{{route('dahboardTool')}}',
        type: 'post',
        data: pdata,
        success: function(response) {
          $('#modalBody').html(response);
          $('#empModal').modal('show');
          $('.selectpicker').selectpicker({
            noneSelectedText: 'Seçim Yapmalısınız'
          });
        }
      });
    });
    $('.addtool').click(function() {
      $('.addtype').data('row', $(this).data('row'));
      $('.addtype').data('index', $(this).data('index'));
      $('#toolTypeModal').modal('show');
    });
    $('.deleteModal').click(function() {
      $("input[name='row']").val($(this).data('row'));
      $("input[name='tool']").val($(this).data('tool'));
      $("input[name='action_type']").val($(this).data('action_type'));
      $('#deleteModal').modal('show');
    });
  });
</script>
@stop
