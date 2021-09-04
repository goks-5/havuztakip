<div class="row display-flex">
  <div class="col-xs-12 col-sm-8  col-md-8 col-lg-8 mb-o row1_5">
    @include('dashboard.rows.widget',['row'=>$row,'index'=>1])
  </div>
  <div class="col-xs-12 col-sm-4  col-md-4 col-lg-4 mb-o row5">
    @include('dashboard.rows.widget',['row'=>$row,'index'=>2])
    @include('dashboard.rows.widget',['row'=>$row,'index'=>3])
    @include('dashboard.rows.widget',['row'=>$row,'index'=>4])
    @include('dashboard.rows.widget',['row'=>$row,'index'=>5])
  </div>
</div>
