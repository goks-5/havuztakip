<div class="tool_text">
    @include('dashboard.tools.toolSettings',['tool'=>$tool])
    @if ($settings['title'] != '')
        <div class="col-12 tool_data_title" style="@foreach ($settings['css'] as $key=> $value) {{ $key }}:{{ $value }}; @endforeach">
            {{ $settings['title'] }}
        </div>
    @endif
    <div class="col-12">
        <input id="switch_tool_{{ $tool->id }}" type="checkbox" data-toggle="toggle"
            data-onstyle="{{ $settings['onstyle'] }}" data-offstyle="{{ $settings['offstyle'] }}"
            data-on="{{ $settings['on'] }}" data-off="{{ $settings['off'] }}" data-onvalue="{{ $settings['onValue'] }}"
            data-offvalue="{{ $settings['offValue'] }}">
    </div>
</div>

@push('javascript')
<script>
    $("#switch_tool_{{ $tool->id }}").change(function() {
        if ($(this).prop("checked") == true) {
            status = 'on';
        } else if ($(this).prop("checked") == false) {
            status = 'off';
        }
        $.ajax({
            url: "{{route('butondata')}}",
            data: "tool_id={{ $tool->id }}&status=" + status,
            type: 'get',
            success: function() {
                console.log(result);
            }
        });
    });
</script>
@endpush
