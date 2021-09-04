<div>
  <div class='row'>
    <div class="col-xs-12">
    <h5> {{$equipment->env_code}} {{$equipment->name}} {{$maintenance->title}} Bakımı</h5>
   </div>
  </div>
  <div class='row'>
    <div class="col-xs-12">
     <span>{{$maintenance->notes}} </span>
   </div>
  </div>
  <div class='row'>
    <div class="col-xs-12">
      @if(isset($maintenance->file))
        @php
        $file = json_decode($maintenance->file,true);
          if(isset($file[0])){
            $file = $file[0];
          }        
        @endphp
            @if(isset($file['download_link']))
              Bakım Talimatı :
      <a class="fileType" target="_blank" href="{{ Storage::disk(config('voyager.storage.disk'))->url($file['download_link']) }}">
         {{$file['original_name']}}
      </a>
    @endif
  @endif
   </div>
  </div>


<form method="post" action="{{route('task_edit')}}" enctype="multipart/form-data">
  @csrf

  <input type="hidden" name="save" value="1" />
  <input type="hidden" name="task_week_id" value="{{$task_week_id}}" />
  <input type="hidden" name="maintenance_id" value="{{$maintenance->id}}" />
  <input type="hidden" name="equipment_id" value="{{$equipment->id}}" />
  <div class="form-group row">
    <div class="col-xs-4">
      <label class="control-label" for="name">Tarih</label>
      @php
      if(isset($task->task_date)){
      $date = date('Y-m-d',strtotime($task->task_date));
      }
      @endphp
      <input type="date" name="task_date" value="{{$date ?? date('Y-m-d')}}" class="form-control" />
    </div>
    <div class="col-xs-4">
      <label class="control-label" for="name">Bakımı Yapan</label>
      <select class="form-control selectpicker" name="task_user_id">
          <option value="">Personel Seçin</option>
        @foreach ($staffs as $staff)
        <option value="{{$staff->id}}" @if(isset($task->task_user_id) && $task->task_user_id == $staff->id) selected @endif>
            {{$staff->name}} | {{$staff->department}}
        </option>
        @endforeach
      </select>
    </div>
    <div class="col-xs-4">
      <label class="control-label" for="name">Durum</label>
      <select class="form-control selectpicker" name="status">
        <option value="Tamamlandı" @if(isset($task->status) && $task->status =="Tamamlandı") selected @endif>Tamamlandı</option>
        <option value="Bakıma Başlandı" @if(isset($task->status) && $task->status =="Bakıma Başlandı") selected @endif>Bakıma Başlandı</option>
        <option value="Firma Yönlendirildi" @if(isset($task->status) && $task->status =="Firma Yönlendirildi") selected @endif>Firma Yönlendirildi</option>
        <option value="Malzeme Bekliyor" @if(isset($task->status) && $task->status =="Malzeme Bekliyor") selected @endif>Malzeme Bekliyor</option>
        <option value="Bekliyor" @if(isset($task->status) && $task->status =="Bekliyor") selected @endif>Bekliyor</option>
      </select>
    </div>
  </div>
  <div class="form-group row">
    <div class="col-xs-6">
      <label class="control-label" for="name">Dosya</label>
      @if(isset($task->file))
        @php
        $file = json_decode($task->file,true);
        @endphp
            @if(isset($file['download_link']))
        <div data-field-name="file">
          <a class="fileType" target="_blank" href="{{ Storage::disk(config('voyager.storage.disk'))->url($file['download_link']) }}">
             {{$file['original_name']}}
          </a>

        </div>

                @endif
                        @endif

        <input type="file" name="file">
    </div>
    <div class="col-xs-6">
      <label class="control-label" for="name">Not</label>
      <textarea name="note" class="form-control">{{$task->note ?? ""}}</textarea>
    </div>
  </div>



  <div class="form-group">
    <input type="submit" value="Kaydet" class="btn btn-primary save" />
  </div>
</form>
