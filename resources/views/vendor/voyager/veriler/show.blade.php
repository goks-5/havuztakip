@extends('voyager::master')

@section('css')
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
    .header-bar {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        margin-top: 30px; /* 🔼 Buraya eklendi */
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 2px solid #ddd;
    }

    .header-title {
        font-size: 24px;
        font-weight: bold;
        color: #000;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
    }

    .header-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    #rangePicker {
        width: 250px !important;
    }

    select[name="period"] {
        width: 160px !important;
    }

    .header-filters .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 38px;
        width: 38px;
        padding: 0;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: flex-end;
        margin-top: 20px;
    }

    #veri-tablo-tum {
        display: none;
    }
</style>
@stop

@section('page_title', 'Veriler - ' . $device->name)

@section('content')
<div class="container-fluid">
    {{-- Başlık ve Filtre Alanı --}}
    <div class="header-bar">
        <div class="header-title">
            <i class="voyager-eye"></i> {{ $device->name }}
        </div>
        <form method="get" action="" class="header-filters">
            <input type="text" name="range" id="rangePicker" class="form-control form-control-sm"
                   placeholder="Tarih Aralığı" value="{{ request('range') }}" />

            <select name="period" class="form-control form-control-sm">
                <option disabled {{ request('period') ? '' : 'selected' }}>Etiket Türü</option>
                <option value="endeks"   {{ request('period') == 'endeks'   ? 'selected' : '' }}>Endeks</option>
                <option value="saatlik"  {{ request('period') == 'saatlik'  ? 'selected' : '' }}>Saatlik</option>
                <option value="günlük"   {{ request('period') == 'günlük'   ? 'selected' : '' }}>Günlük</option>
                <option value="haftalık" {{ request('period') == 'haftalık' ? 'selected' : '' }}>Haftalık</option>
                <option value="aylık"    {{ request('period') == 'aylık'    ? 'selected' : '' }}>Aylık</option>
                <option value="yıllık"   {{ request('period') == 'yıllık'   ? 'selected' : '' }}>Yıllık</option>
                <option value="tümü"     {{ request('period') == 'tümü'     ? 'selected' : '' }}>Tümü</option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="voyager-search"></i>
            </button>

            <button type="button" class="btn btn-success btn-sm" id="btn-export-excel">
                <i class="voyager-file-text"></i>
            </button>

            <a href="{{ url('veriler') }}" class="btn btn-warning btn-sm">
                <i class="voyager-angle-left"></i>
            </a>
        </form>
    </div>

    {{-- PHP gruplama --}}
    @php
        $allTags = json_decode($device->tags, true) ?? [];
        $tagList = collect($allTags)->filter(fn($label, $id) => $id < 100)->toArray();

        $grouped = collect($datas ?? [])->groupBy(function($item){
            return \Carbon\Carbon::parse($item->created_at)->format('d.m.Y H:i');
        });

        $currentPage = request()->get('page', 1);
        $perPage = 30;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $grouped->forPage($currentPage, $perPage),
            $grouped->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    @endphp

    {{-- Görünen Tablo --}}
    <div class="table-responsive">
        <table id="veri-tablo" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Tarih</th>
                    @foreach($tagList as $id => $label)
                        <th>{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($paginated as $timestamp => $cells)
                    <tr>
                        <td>{{ $timestamp }}</td>
                        @foreach($tagList as $baseId => $label)
                            @php
                                $periodLabel = ucfirst(request('period')) == 'Endeks' ? '' : ' ' . ucfirst(request('period'));
                                $data_id = collect($tags)->search(trim($label . $periodLabel));
                                $value = optional($cells->firstWhere('data_id', (int) $data_id))->value;
                            @endphp
                            <td>{{ $value ?? '-' }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($tagList) + 1 }}" class="text-center">Veri bulunamadı.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $paginated->links() }}
        </div>
    </div>

    {{-- Excel için tüm veriler tablosu --}}
    <table id="veri-tablo-tum" class="table">
        <thead>
            <tr>
                <th>Tarih</th>
                @foreach($tagList as $id => $label)
                    <th>{{ $label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($grouped as $timestamp => $cells)
                <tr>
                    <td>{{ $timestamp }}</td>
                    @foreach($tagList as $baseId => $label)
                        @php
                            $periodLabel = ucfirst(request('period')) == 'Endeks' ? '' : ' ' . ucfirst(request('period'));
                            $data_id = collect($tags)->search(trim($label . $periodLabel));
                            $value = optional($cells->firstWhere('data_id', (int) $data_id))->value;
                        @endphp
                        <td>{{ $value ?? '-' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@stop

@section('javascript')
    <script src="//cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        $(function () {
            let start = moment().startOf('day'), end = moment();
            if (getUrlParam('range')) {
                let parts = decodeURIComponent(getUrlParam('range')).split(' - ');
                start = moment(parts[0], 'DD.MM.YYYY HH:mm');
                end = moment(parts[1], 'DD.MM.YYYY HH:mm');
            }
            $('#rangePicker').daterangepicker({
                timePicker: true,
                timePicker24Hour: true,
                locale: { format: 'DD.MM.YYYY HH:mm' },
                startDate: start,
                endDate: end
            });

            $('#btn-export-excel').on('click', function () {
                const table = document.querySelector("#veri-tablo-tum");
                const wb = XLSX.utils.table_to_book(table, { sheet: "Tüm Veriler" });
                XLSX.writeFile(wb, "veriler.xlsx");
            });

            function getUrlParam(key) {
                return new URLSearchParams(window.location.search).get(key);
            }
        });
    </script>
@stop
