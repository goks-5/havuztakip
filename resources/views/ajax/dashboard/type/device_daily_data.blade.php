  @include('ajax.dashboard.type.head',['multiple'=>'false'])

    <div class="form-group row">
    <div class="col-xs-4">
        <label class="control-label" for="name">Gün Başlangıç Saati</label>
          <select class="form-control select2" name="setting[start_hour]">
            <option value="0" {{isset($settings['start_hour']) && $settings['start_hour'] == "0" ? 'selected':''}}>00</option>
            <option value="1" {{isset($settings['start_hour']) && $settings['start_hour'] == "1" ? 'selected':''}}>01</option>
            <option value="2" {{isset($settings['start_hour']) && $settings['start_hour'] == "2" ? 'selected':''}}>02</option>
            <option value="3" {{isset($settings['start_hour']) && $settings['start_hour'] == "3" ? 'selected':''}}>03</option>
            <option value="4" {{isset($settings['start_hour']) && $settings['start_hour'] == "4" ? 'selected':''}}>04</option>
            <option value="5" {{isset($settings['start_hour']) && $settings['start_hour'] == "5" ? 'selected':''}}>05</option>
            <option value="6" {{isset($settings['start_hour']) && $settings['start_hour'] == "6" ? 'selected':''}}>06</option>
            <option value="7" {{isset($settings['start_hour']) && $settings['start_hour'] == "7" ? 'selected':''}}>07</option>
            <option value="8" {{isset($settings['start_hour']) && $settings['start_hour'] == "8" ? 'selected':''}}>08</option>
            <option value="9" {{isset($settings['start_hour']) && $settings['start_hour'] == "9" ? 'selected':''}}>09</option>
            <option value="10" {{isset($settings['start_hour']) && $settings['start_hour'] == "10" ? 'selected':''}}>10</option>
            <option value="11" {{isset($settings['start_hour']) && $settings['start_hour'] == "11" ? 'selected':''}}>11</option>
            <option value="12" {{isset($settings['start_hour']) && $settings['start_hour'] == "12" ? 'selected':''}}>12</option>
            <option value="13" {{isset($settings['start_hour']) && $settings['start_hour'] == "13" ? 'selected':''}}>13</option>
            <option value="14" {{isset($settings['start_hour']) && $settings['start_hour'] == "14" ? 'selected':''}}>14</option>
            <option value="15" {{isset($settings['start_hour']) && $settings['start_hour'] == "15" ? 'selected':''}}>15</option>
            <option value="16" {{isset($settings['start_hour']) && $settings['start_hour'] == "16" ? 'selected':''}}>16</option>
            <option value="17" {{isset($settings['start_hour']) && $settings['start_hour'] == "17" ? 'selected':''}}>17</option>
            <option value="18" {{isset($settings['start_hour']) && $settings['start_hour'] == "18" ? 'selected':''}}>18</option>
            <option value="19" {{isset($settings['start_hour']) && $settings['start_hour'] == "19" ? 'selected':''}}>19</option>
            <option value="20" {{isset($settings['start_hour']) && $settings['start_hour'] == "20" ? 'selected':''}}>20</option>
            <option value="21" {{isset($settings['start_hour']) && $settings['start_hour'] == "21" ? 'selected':''}}>21</option>
            <option value="22" {{isset($settings['start_hour']) && $settings['start_hour'] == "22" ? 'selected':''}}>22</option>
            <option value="23" {{isset($settings['start_hour']) && $settings['start_hour'] == "23" ? 'selected':''}}>23</option>
          </select>
    </div>

      <div class="col-xs-4">
        <label class="control-label" for="name">Gün</label>
          <select class="form-control select2" name="setting[day]">
            <option value="0" {{isset($settings['day']) && $settings['day'] == "0" ? 'selected':''}}>Bugün</option>
            <option value="1" {{isset($settings['day']) && $settings['day'] == "1" ? 'selected':''}}>Dün</option>
            <option value="2" {{isset($settings['day']) && $settings['day'] == "2" ? 'selected':''}}>Önceki Gün</option>
          </select>
      </div>

</div>
