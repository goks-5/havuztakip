<div class="form-group">
  <label for="image">Arka Plan</label>

  <input type="file" class="form-control-file" name="backgroud" id="image">
  @if(isset($page->img))
    <img src="../images/{{$page->img}}" style="max-width: 200px;" />
  @endif
</div>
<div class="form-group">
      <label class="control-label">Katman</label>
      <input type="number" name="order" class="form-control" value="{{$order ?? '999'}}" />
</div>

