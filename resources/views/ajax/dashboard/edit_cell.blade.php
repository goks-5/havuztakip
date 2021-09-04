<form method="post" action="{{route('dahboardTool')}}">
  {{ csrf_field() }}
  <input type="hidden" name="index" value="{{$index}}" />
  <input type="hidden" name="row" value="{{$row}}" />
  <input type="hidden" name="action_type" value="edit_cell" />
  <input type="hidden" name="save" value="1" />
  <div class="form-group row">
      <div class="col-xs-8">
    <label class="control-label" for="name">Başlık</label>
    <input type="text" name="title" value="{{$title}}" class="form-control" />
  </div>
  <div class="col-xs-4">
    <label class="control-label" for="name">Arka Plan Rengi</label>
    <input type="color" name="color" value="{{$color ?: '#f0f0f0'}}" class="form-control" />
  </div>
  </div>
  <div class="modal-footer">

     <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
    <input type="submit" value="Kaydet" class="btn btn-primary save" />
  </div>
</form>
