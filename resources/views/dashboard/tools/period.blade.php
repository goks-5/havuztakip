<div class="tool_data row ">
  @include('dashboard.tools.toolSettings',['tool'=>$tool])
    @php
    $setting = json_decode($tool->settings,true);
    @endphp

    <table id="tool_{{$tool->id}}" class="table table-hover">

    </table>

</div>
@push('javascript')

@endpush
