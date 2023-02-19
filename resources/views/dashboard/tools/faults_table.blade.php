<div class="tool_data row">
    @include('dashboard.tools.toolSettings', ['tool' => $tool])

    <div id="tool_{{ $tool->id }}"></div>

    @can('browse', app('App\Fault'))
        <div class="table-responsive">
            <table id="faults_table_tool_{{ $tool->id }}" class="table table-hover">
                <thead>
                    <tr>
                        <th>Oluşturma</th>
                        <th>Durum</th>
                        <th>Ekipman</th>
                        <th>Arıza Kodu</th>
                        <th>Bildiren Personel</th>
                        <th>Bakımcı</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
        <style>
            .bekliyor {
                background: #FF5;
                color: #526069;
            }

            .basladi {
                background: #FF8;
                color: #526069;
            }

            .yonlendirildi {
                background: #aef;
                color: #526069;
            }

            .m_bekliyor {
                background: #aef;
                color: #526069;
            }

            .onay {
                background: #9F9;
                color: #526069;
            }

            .yeni {
                background: #F33;
                color: #FFF;
            }
        </style>
    @endcan
    @can('add', app('App\Fault'))
    @endcan

</div>
