<div class="row display-flex">
  <div class="col-xs-12 col-sm-8  col-md-7 col-lg-7 mb-o  row1_2">
      @include('dashboard.rows.widget',['row'=>$row,'index'=>1])
  </div>
  <div class="col-xs-12 col-sm-4  col-md-5 col-lg-5 mb-o  row2">
      @include('dashboard.rows.widget',['row'=>$row,'index'=>2])
      @include('dashboard.rows.widget',['row'=>$row,'index'=>3])
  </div>
</div>
