<form method="post" action="{{route('dahboardTool')}}">
  {{ csrf_field() }}
  <input type="hidden" name="action_type" value="edit_row" />
  <input type="hidden" name="row" value="{{$row}}" />
  <input type="hidden" name="save" value="1" />
@include('ajax.dashboard.row_type')
<div class="modal-footer">

   <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
  <input type="submit" value="Kaydet" class="btn btn-primary save" />
</div>
</form>
