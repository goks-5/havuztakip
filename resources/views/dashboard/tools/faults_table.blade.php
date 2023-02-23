<div class="tool_data row">
    @include('dashboard.tools.toolSettings', ['tool' => $tool])
    @php
        $staffs = App\Staff::select('*')
            ->where('company_id', Auth::user()->company_id)
            ->get();
        
    @endphp
    <div id="tool_{{ $tool->id }}"></div>

    @can('browse', app('App\Fault'))
        <div class="table-responsive">
            <table id="faults_table_tool_{{ $tool->id }}" class="table table-hover">
                <thead>
                    <tr>
                        <th>Durum</th>
                        <th>Ekipman - Arıza Kodu</th>
                        <th>Oluşturma</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>

        <div class="modal fade" id="acceptModal" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Arızayı Kabul Et</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('faultsActions') }}" method="POST">
                            {{ csrf_field() }}
                            <input type="hidden" name='action' value='accept' />
                            <input type="hidden" name='id' class='modalidinput' />
                            <div class="form-group row">
                                <label for="maintainer_id" class="col-md-4">Bakımcı</label>
                                <select class="selector col-md-8" name='maintainer_id'>
                                    <option value=''>Bakım Elemanı Seçin</option>
                                    @foreach ($staffs as $staff)
                                        <option value='{{ $staff->id }}'>{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="submit" class="btn btn-danger pull-right delete-confirm" value="Kabul Et">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="actionModal" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Arıza İşlemi</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('faultsActions') }}" method="POST">

                            <input type="hidden" name='action' value='action' />

                            <input type="hidden" name='id' class='modalidinput' />
                            <div class="form-group row">
                                <label for="status" class="col-md-4">İşlem</label>
                                <select class="selector col-md-8" name='status'>

                                    <option value='Bekliyor |0|'>Bekliyor</option>
                                    <option value='Bakıma Başlandı |0|'>Bakıma Başlandı</option>
                                    <option value='Firma Yönlendirildi |2|'>Firma Yönlendirildi</option>
                                    <option value='Malzeme Bekliyor |2|'>Malzeme Bekliyor</option>
                                    <option value='Onay |1|'>Tamamlandı</option>

                                </select>
                            </div>
                            <div class="form-group row">
                                <label for="note" class="col-md-4">Açıklama</label>
                                <textarea name="maintainer_note" class="col-md-8" rows="6"></textarea>
                            </div>
                            {{ csrf_field() }}
                            <input type="hidden" name='id' class='modalidinput' />
                            <input type="submit" class="btn btn-danger pull-right delete-confirm" value="İşlem Gir">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="closeModal" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Arızayı Kapat</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('faultsActions') }}" method="POST">
                            {{ csrf_field() }}

                            <input type="hidden" name='action' value='close' />
                            <input type="hidden" name='id' class='modalidinput' />
                            <input type="submit" class="btn btn-danger pull-right delete-confirm" value="Arızayı Kapat">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>




        <style>
            .bekliyor td {
                background: #FF5;
                color: #526069;
            }

            .basladi td {
                background: #FF8;
                color: #526069;
            }

            .yonlendirildi td {
                background: #aef;
                color: #526069;
            }

            .m_bekliyor td {
                background: #aef;
                color: #526069;
            }

            .onay td {
                background: #9F9;
                color: #526069;
            }

            .yeni td {
                background: #F33;
                color: #FFF;
            }
        </style>
    @endcan
    @can('add', app('App\Fault'))
    @endcan

</div>
