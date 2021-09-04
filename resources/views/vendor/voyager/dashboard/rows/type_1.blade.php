<div class="row">
  <div class="col-xs-12 col-sm-6  col-md-3 col-lg-3 mb-o">
    <div class="widget">
      @if (!empty($row->title_1))
      <div class="widget-header"><span>{{$row->title_1}}</span></div>
      @endif
      <div class="widget-tools">
       @foreach ($row->tools[1] as $tool)
           @include('dashboard.tools.'.$tool->type,['tool'=>$tool])
       @endforeach
      </div>
    </div>
  </div>
  <div class="col-xs-12 col-sm-6  col-md-3 col-lg-3 mb-o">
    <div class="widget">
      @if (!empty($row->title_2))
      <div class="widget-header"><span>{{$row->title_2}}</span></div>
      @endif
      <div class="widget-tools">
        2
      </div>
    </div>
  </div>
  <div class="col-xs-12 col-sm-6  col-md-3 col-lg-3 mb-o">
    <div class="widget">
      @if (!empty($row->title_3))
      <div class="widget-header"><span>{{$row->title_3}}</span></div>
      @endif
      <div class="widget-tools">
        3

      </div>
    </div>
  </div>
  <div class="col-xs-12 col-sm-6  col-md-3 col-lg-3 mb-o">
    <div class="widget">
      @if (!empty($row->title_4))
      <div class="widget-header"><span>{{$row->title_4}}</span></div>
      @endif
      <div class="widget-tools">
        4

      </div>
    </div>
  </div>
</div>
