<div class="tool_data row">
    @include('dashboard.tools.toolSettings', ['tool' => $tool])

    <div id="tool_{{ $tool->id }}"></div>

    @can('browse', app('App\Fault'))
        <div class="table-responsive">
            <table id="faults_table_tool_{{ $tool->id }}" class="table table-hover">
                <thead>
                    <tr>
                        <th>Durum</th>
                        <th>Ekipman</th>
                        <th>Arıza Kodu</th>
                        <th>Oluşturma</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
        <style>
            .bekliyor td{
                background: #FF5;
                color: #526069;
            }

            .basladi td{
                background: #FF8;
                color: #526069;
            }

            .yonlendirildi td{
                background: #aef;
                color: #526069;
            }

            .m_bekliyor td{
                background: #aef;
                color: #526069;
            }

            .onay td{
                background: #9F9;
                color: #526069;
            }

            .yeni td{
                background: #F33;
                color: #FFF;
            }
        </style>
    @endcan
    @can('add', app('App\Fault'))
    @endcan

</div>
