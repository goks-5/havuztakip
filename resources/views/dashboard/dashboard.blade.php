@extends('voyager::master')

@section('content')





<div class="page-content" style="min-height:800px">
  <div class="boards" >

    <ul class="nav nav-tabs">
      @foreach ($boards as $key => $value)
      <li class="@if($value->id == $board->id)active @endif" >
      <a class="boardlink" href="{{route('dashboardnew' , $value->id)}}" data-id="{{$value->id}}">{{$value->title}}</a>
       <a class="boardcopy edithide" data-board="enerjiboard-{{$value->id}}" style="right: 45px;"><i class="voyager-images"></i></a>
      <a class="boardpaste edithide" data-board="{{$value->id}}" style="right: 25px;"><i class="voyager-wand"></i></a>
      <a class="boardDelete deleteModal edithide" data-action_type="delete_board" data-board="{{$value->id}}"><i class="voyager-x"></i></a>
     
      </li>
      @endforeach
      <li style="min-width: unset;"  class="edithide">
        <a  href="#" class="addBoard" style="font-size: 30px;font-weight: 500;line-height: 17px;">+</a>
      </li>
    </ul>
  </div>
  <label class="checkbox-inline dashboardmenu" style="padding: 0px 0px;">
  <input class="dashboardmenu" type="checkbox" data-toggle="toggle" data-size="mini" data-width="50" data-on="<i class='voyager-tools'></i>" data-off="<i class='voyager-lock'></i>" id="editOnOff"> </label>
  <a class="dashboardmenu edithide" data-toggle="modal" href="#toolTypeModal" style="right: 80px;font-size: 24px;"><i class="voyager-plus"></i></a>

<i class="fas fa-unlock-alt"></i>


  @include('voyager::alerts')
  @include('voyager::dimmers')


  @foreach ($tools as $tool)
  @php
  $settings = array();
  $settings = json_decode($tool->settings,true);
  if (isset($settings['css'])){
    echo "<style>";
    echo "#td_".$tool->id."{";
    echo "text-align:center;align-content:center;";
foreach ($settings['css'] as $key => $value) {
  echo "$key:$value;";
}
echo "}";
if (isset($settings['css']['background']) && $settings['css']['background'] == 'rgba(0, 0, 0, 0)') {
    echo "#td_".$tool->id.":before{border:0!important}";

}
    echo "</style>";
  }
  @endphp
  <div class="ui-widget-content resizable" data-tool="{{$tool->id}}" id="td_{{$tool->id}}" style="{{ $tool->style }}" >
    @include('dashboard.tools.'.$tool->type,['tool'=>$tool,'settings'=>$settings])
  </div>
  @endforeach



</div>

@include('widget.ajaxModal')


<div class="modal fade" id="toolTypeModal" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4>Araç Tipi Seçin</h4>
      </div>
      <div>
        <ul class="row">

@foreach ($toolSets as $toolSet)
  <li class="deviceTypeList  ajaxmodal addtype col-xs-3" data-dismiss="modal" data-type="{{$toolSet->slug}}" data-board="{{$board->id}}" data-action_type="add_tool" style="background:url('{{ Voyager::image( $toolSet->image ) }}')" title="{{$toolSet->name}}">
  </li>
@endforeach
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
        <form method="post" action="{{route('boardAction')}}">
          {{ csrf_field() }}
          <input type="hidden" name="tool" />
          <input type="hidden" name="board" />
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

<div class="modal fade" id="addBoard" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4>Yeni İzleme Ekranı Ekle</h4>
      </div>
      <form method="post" action="{{route('boardAction')}}">
        <div class="content">
          {{ csrf_field() }}
          <input type="hidden" name="action_type" value="addBoard"/>
          <div class="form-group row">
            <div class="col-xs-2">
            </div>
            <div class="col-xs-8">
              <label class="control-label">İzleme Ekranı Adı</label>
              <input type="text" name="title" class="form-control" required="">
            </div>
            <div class="col-xs-2">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
          <input type="submit" value="Kaydet" class="btn btn-primary save">
        </div>
      </form>
    </div>
  </div>
</div>



@stop

@section('css')
<link href="https://fonts.googleapis.com/css?family=Quantico&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.5.4/bootstrap-select.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<style>
  .voyager .boards .nav-tabs {
    background: none;
    border-bottom: 0px;
  }

  .voyager .boards .nav-tabs .active a {
    border: 0px;
  }

  .voyager .boards .nav-tabs a i {
    display: block;
    font-size: 22px;
  }

  .voyager .boards .nav-tabs>li {
    margin-bottom: -1px !important;
    min-width: 100px;
  }

  .voyager .boards .nav-tabs a {
    text-align: center;
    background: #f8f8f8;
    border: 1px solid #f1f1f1;
    position: relative;
    top: -1px;
    border-bottom-left-radius: 0px;
    border-bottom-right-radius: 0px;
  }

  .voyager .boards .nav-tabs a i {
    display: block;
    font-size: 22px;
  }

  .nav-tabs>li.active>a,
  .nav-tabs>li.active>a:focus,
  .nav-tabs>li.active>a:hover {
    background: #fff !important;
    color: #555 !important;
    border-bottom: 1px solid #fff !important;
    top: -1px !important;
  }

  .nav-tabs>li a {
    padding: 5px 20px;
    transition: all 0.3s ease;
  }


  .nav-tabs>li.active>a:focus {
    top: 0px !important;
  }
  .boardDelete ,
  .boardcopy ,
  .boardpaste {
    position: absolute!important;
    margin: 0px!important;
    padding: 0px!important;
    color: #555!important;
    opacity: .15;
    filter: alpha(opacity=10);
    z-index: 2;
    right: 5px;
  }
  .boardDelete i ,
  .boardcopy i ,
  .boardpaste i {
    font-size: 16px!important;
  }
  .boardDelete i:hover ,
   .boardcopy i:hover ,
   .boardpaste i:hover
   {
    border-radius: 9px;
    background: #9E9E9E;
    color: #000!important;
    line-height: 16px;
}
  .voyager .boards .nav-tabs>li>a:hover {
    background-color: #fff !important;
  }

  .resizable {
    position: absolute;
    top: 110px;
    left: 70px;
    width: 160px;
    height: 100px;
    border-width: 2.5px;
    border-color: #f9f9f9;
  }

  .tool_data {
    margin: 1px !important;
    height: calc(100% - 2px) !important;
    width: calc(100% - 2px) !important;
  }

  .resizable:before {
    position: absolute;
    display: contents;
    content: '';
    border: 1px solid #9E9E9E;
    height: 100%;
    width: 100%;
    box-sizing: border-box;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
  }

  .ui-icon-gripsmall-diagonal-se {
    opacity: 0.1;
    filter: alpha(opacity=10);
  }

  .ui-icon-gripsmall-diagonal-se:hover {
    opacity: 1;
    filter: alpha(opacity=100);
  }


  .dashboardmenu {
    right: 10px;
    top: 62px;
    position: absolute;
  }

  .dashboardmenu .dropdown-content {
    transform: translate3d(-200px, -20px, 0px);
  }

  @media only screen and (max-width: 768px) {
    .resizable {
      position: relative;
      top: unset !important;
      left: unset !important;
      width: 100% !important;
    }
  }
</style>

@stop


@section('javascript')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.5.4/bootstrap-select.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script type="text/javascript">
  google.charts.load('current', {
    'packages': ['gauge', 'line', 'corechart', 'bar' ,'table'],
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
        url: '{{route('boardAction')}}',
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

    $('.deleteModal').click(function() {
      $("input[name='tool']").val($(this).data('tool'));
        $("input[name='board']").val($(this).data('board'));
      $("input[name='action_type']").val($(this).data('action_type'));
      $('#deleteModal').modal('show');
    });
    $('.addBoard').click(function() {
      $('#addBoard').modal('show');
    });
  });


  $(document).ready(function() {
    $(".boardcopy").click(function() {
        var boardData = $(this).data("board"); // data-board değerini al

        // Bir textarea oluşturarak içine veriyi yerleştir
        var textarea = document.createElement("textarea");
        textarea.value = boardData;

        // Dokümanın sonuna textarea elemanını ekle
        document.body.appendChild(textarea);

        // Veriyi seç ve kopyala
        textarea.select();
        document.execCommand("copy");

        // Artık textarea'ya ihtiyaç yok, kaldırabiliriz
        document.body.removeChild(textarea);
        alert("Board panoya kopyalandı ");

    });

    $(".boardpaste").click(function() {
        var target = $(this).data("board"); // data-board değerini al


        navigator.clipboard.readText().then(function(sboard) {
           if(sboard.startsWith('enerjiboard-') ){        
              sboard = sboard.replace('enerjiboard-', '');
                if ($sboard != target){
                      $.ajax({
                      url: '{{route('dashboarddata')}}' + '/' + sboard + '/' + target , 
                      method: "GET",
                      success: function(response) {
                      location.reload();
                      },
                      error: function(xhr, status, error) {
                      alert("Veri alınamadı:", error);
                      }
                      });
              } else{
                alert("Hedefle kaynak aynı olamaz ");
              }
            }else{
              alert("Panodan board yok ");
            }         
           
        }).catch(function(err) {
            alert("Panodan veri alınamadı: ", err);
        });

        
    });
});
  
</script>
@stop
