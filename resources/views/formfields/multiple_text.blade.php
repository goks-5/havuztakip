<input type="hidden" id="form-{{ $row->field }}" name="{{ $row->field }}"
    value="{{ old($row->field, $dataTypeContent->{$row->field} ?? ($options->default ?? '')) }}">
    <input type="hidden" id="form-type" name="type" value="{{ old('type',$dataTypeContent->type ?? '') }}">
    <input type="hidden" id="form-offset" name="offset" value="{{ old('offset',$dataTypeContent->offset ?? '') }}">
    <input type="hidden" id="form-multiplier" name="multiplier" value="{{ old('multiplier',$dataTypeContent->multiplier ?? '') }}">

<div class='con_{{ $row->field }}'>
    <div class="form-group mtextrow">
        <div class="row">
            <div class="col-sm-2">
                <input type="text" data-name="" data-index="0" class="form-control ginput multiple_{{ $row->field }}"
                    name="__{{ $row->field }}[0]"
                    placeholder="0. {{ old($row->field, $options->placeholder ?? $row->getTranslatedAttribute('display_name')) }}">
            </div>

            <div class="col-sm-1">
                <input type="number" class="form-control offset"
                    name="__offset[0]"
                    placeholder="OffSet">
            </div>

            <div class="col-sm-1">
                <input type="number" class="form-control multiplier"
                    name="__multiplier[0]"
                    placeholder="Multiplier">
            </div>

            <div class="col-sm-2">
                <select class="form-control type" name="__type[0]" id="type_0">
                    <option value='diff'>Fark Değer</option>
                    <option value='last'>Son Değer</option>
                    <option value='first'>İlk Değer</option>
                    <option value='avg'>Ortalama Değer</option> 
                    <option value='sum'>Toplam Değer</option> 
                    <option value='max'>En Büyük Değer</option> 
                    <option value='min'>En Küçük Değer</option>
                    <option value='triger[0]'>0:00 da tetiklen</option>
                    <option value='triger[1]'>1:00 da tetiklen</option>
                    <option value='triger[2]'>2:00 da tetiklen</option>
                    <option value='triger[3]'>3:00 da tetiklen</option>
                    <option value='triger[4]'>4:00 da tetiklen</option>
                    <option value='triger[5]'>5:00 da tetiklen</option>
                    <option value='triger[6]'>6:00 da tetiklen</option>
                    <option value='triger[7]'>7:00 da tetiklen</option>
                    <option value='triger[8]'>8:00 da tetiklen</option>
                    <option value='triger[9]'>9:00 da tetiklen</option>
                    <option value='triger[10]'>10:00 da tetiklen</option>
                    <option value='triger[11]'>11:00 da tetiklen</option>
                    <option value='triger[12]'>12:00 da tetiklen</option>
                    <option value='triger[13]'>13:00 da tetiklen</option>
                    <option value='triger[14]'>14:00 da tetiklen</option>
                    <option value='triger[15]'>15:00 da tetiklen</option>
                    <option value='triger[16]'>16:00 da tetiklen</option>
                    <option value='triger[17]'>17:00 da tetiklen</option>
                    <option value='triger[18]'>18:00 da tetiklen</option>
                    <option value='triger[19]'>19:00 da tetiklen</option>
                    <option value='triger[20]'>20:00 da tetiklen</option>
                    <option value='triger[21]'>21:00 da tetiklen</option>
                    <option value='triger[22]'>22:00 da tetiklen</option>
                    <option value='triger[23]'>23:00 da tetiklen</option>

                </select>
            </div>


            <div class="col-sm-1">
                <div class="form-check">
                    <input class="form-check-input  ginput multiple_{{ $row->field }}" type="checkbox" data-index="100"
                        data-name=" Saatlik" value="" id="{{ $row->field }}_100" name="__{{ $row->field }}[100]">
                    <label class="form-check-label" for="{{ $row->field }}_100">Saatlik</label>
                </div>
            </div>
            <div class="col-sm-1">
                <div class="form-check">
                    <input class="form-check-input  ginput multiple_{{ $row->field }}" type="checkbox" data-index="200"
                        data-name=" Günlük" value="" id="{{ $row->field }}_200" name="__{{ $row->field }}[200]">
                    <label class="form-check-label" for="{{ $row->field }}_200">Günlük</label>
                </div>
            </div>
            <div class="col-sm-1">
                <div class="form-check">
                    <input class="form-check-input  ginput multiple_{{ $row->field }}" type="checkbox" data-index="300"
                        data-name=" Haftalık" value="" id="{{ $row->field }}_300" name="__{{ $row->field }}[300]">
                    <label class="form-check-label" for="{{ $row->field }}_300">Haftalık</label>
                </div>
            </div>
            <div class="col-sm-1">
                <div class="form-check">
                    <input class="form-check-input  ginput multiple_{{ $row->field }}" type="checkbox" data-index="400"
                        data-name=" Aylık" value="" id="{{ $row->field }}_400" name="__{{ $row->field }}[400]">
                    <label class="form-check-label" for="{{ $row->field }}_400">Aylık</label>
                </div>
            </div>
            <div class="col-sm-1">
                <div class="form-check">
                    <input class="form-check-input  ginput multiple_{{ $row->field }}" type="checkbox" data-index="500"
                        data-name=" Yıllık" value="" id="{{ $row->field }}_500" name="__{{ $row->field }}[500]">
                    <label class="form-check-label" for="{{ $row->field }}_500">Yıllık</label>
                </div>
            </div>
            <div class="col-sm-1">
                <div class="form-check">
                    <input class="form-check-input  ginput multiple_{{ $row->field }}" type="checkbox" data-index="1000"
                        data-name=" Son Değişim" value="" id="{{ $row->field }}_1000"
                        name="__{{ $row->field }}[1000]">
                    <label class="form-check-label" for="{{ $row->field }}_1000">Değişim İzle</label>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <input type="button" class="btn btn-warning"
        value='Yeni {{ old($row->field, $options->placeholder ?? $row->getTranslatedAttribute('display_name')) }} Ekle'
        id='add_{{ $row->field }}' />
    <input type="button" class="btn btn-danger"
        value='Son {{ old($row->field, $options->placeholder ?? $row->getTranslatedAttribute('display_name')) }} Sil'
        id='remove_{{ $row->field }}' />
</div>
@push('javascript')
    <script>
        $(document).ready(function() {
            var $row = $('.mtextrow');

            var kayitli = JSON.parse('[]');
            if ($('#form-{{ $row->field }}').val().length > 0) {
                kayitli = JSON.parse($('#form-{{ $row->field }}').val());

            }
            var kayitlitype = JSON.parse('[]');
            if ($('#form-type').val().length > 0 && $('#form-type').val() != 'null' ) {
                kayitlitype = JSON.parse($('#form-type').val());
            }
            
            var kayitlioffset = JSON.parse('[]');
            if ($('#form-offset').val().length > 0 && $('#form-offset').val() != 'null') {
                kayitlioffset = JSON.parse($('#form-offset').val());
            }

            var kayitlimultiplier = JSON.parse('[]');
            if ($('#form-multiplier').val().length > 0 && $('#form-multiplier').val() != 'null') {
                kayitlimultiplier = JSON.parse($('#form-multiplier').val());
            }
            
      
            for (elem in kayitli) {
                write_{{ $row->field }}(kayitli[elem], elem);
            }

            function write_{{ $row->field }}(item, index) {
                if (index < 100) {
                    if (index != 0) {
                        $row.clone().insertAfter('.mtextrow:last');
                    }
                    var $currentRow = $('.mtextrow:last');
                    $currentRow.find('.ginput').each(function() {

                        this.value = item + $(this).data('name');
                        var yindex = (index * 1) + ($(this).data('index') * 1);
                        this.name = "__{{ $row->field }}[" + yindex + "]";
                        this.id = "{{ $row->field }}_" + yindex;

                        if (yindex >= 100) {
                            $(this).prop("checked", false);
                            if (typeof kayitli[yindex] !== 'undefined') {
                                $(this).prop("checked", true);
                            }
                        } else {
                            this.placeholder = $currentRow.index() +
                                '. {{ old($row->field, $options->placeholder ?? $row->getTranslatedAttribute('display_name')) }}';

                        }

                    });

                    $currentRow.find('.type').each(function() {
                        $(this).val(kayitlitype[index]);
                        this.name = "__type[" + $currentRow.index() + "]";
                        this.id = "type_" + $currentRow.index();

                    });

                    $currentRow.find('.offset').each(function() {
                        $(this).val(kayitlioffset[index]);
                        this.name = "__offset[" + $currentRow.index() + "]";
                        this.id = "offset_" + $currentRow.index();

                    });

                    $currentRow.find('.multiplier').each(function() {
                        $(this).val(kayitlimultiplier[index]);
                        this.name = "__multiplier[" + $currentRow.index() + "]";
                        this.id = "multiplier_" + $currentRow.index();

                    });

                }
            }
    
            $('.con_{{ $row->field }}').on('input', function() {
                $('.con_{{ $row->field }}').each(function() {

                    $(this).find('.ginput').each(function() {
                        if ($(this).data('name') == "") {
                            value = this.value;
                        }
                        this.value = value + $(this).data('name');
                    });
                })

                $('#form-{{ $row->field }}').val(JSON.stringify($('.multiple_{{ $row->field }}')
                    .serializeJSON().__{{ $row->field }}));
                $('#form-type').val(JSON.stringify($('.type').serializeJSON().__type));
                $('#form-offset').val(JSON.stringify($('.offset').serializeJSON().__offset));
                $('#form-multiplier').val(JSON.stringify($('.multiplier').serializeJSON().__multiplier));
            });



            $('#add_{{ $row->field }}').on('click', function() {
                var $newRow = $row.clone().insertAfter('.mtextrow:last');
                var $currentRow = $('.mtextrow:last');
                $newRow.find('.type').each(function() {
                        this.name = "__type[" + $currentRow.index() + "]";
                        this.id = "type_" + $currentRow.index();

                    });
                $newRow.find('.offset').each(function() {
                    this.name = "__offset[" + $currentRow.index() + "]";
                    this.id = "offset_" + $currentRow.index();

                });
                $newRow.find('.multiplier').each(function() {
                    this.name = "__multiplier[" + $currentRow.index() + "]";
                    this.id = "multiplier_" + $currentRow.index();

                });
                $newRow.find('.ginput').each(function() {
                    this.value = '';
                    var yindex = ($currentRow.index() * 1) + ($(this).data('index') * 1);
                    this.name = "__{{ $row->field }}[" + yindex + "]";
                    this.placeholder = $currentRow.index() +
                        '. {{ old($row->field, $options->placeholder ?? $row->getTranslatedAttribute('display_name')) }}';
                });
            });

            $('#remove_{{ $row->field }}').on('click', function() {

                var $currentRow = $('.mtextrow:last');
                if ($currentRow.index() === 0) {
                    alert("İlk Satır Silinmez");
                } else {
                    $currentRow.remove();
                    $('#form-{{ $row->field }}').val(JSON.stringify($('.multiple_{{ $row->field }}')
                        .serializeJSON().__{{ $row->field }}));
                }
            });


        });

    </script>

@endpush
