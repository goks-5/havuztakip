
<form method="post" action="{{$route ??route('dahboardTool')}}" enctype="multipart/form-data">
  {{ csrf_field() }}
  <input type="hidden" name="index" value="{{$index ?? ''}}" />
  <input type="hidden" name="row" value="{{$row ?? ''}}" />
  <input type="hidden" name="board" value="{{$board ?? ''}}" />
  <input type="hidden" name="type" value="{{$type}}" />
  <input type="hidden" name="action_type" value="add_tool" />
  <input type="hidden" name="save" value="1" />
@include('ajax.dashboard.type.'.$type)
<div class="modal-footer">

   <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
  <input type="submit" value="Kaydet" class="btn btn-primary save" />
</div>
</form>
