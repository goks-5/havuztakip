<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DeviceDatasExport implements FromArray, WithHeadings
{
    use Exportable;

    public function __construct(array $datas)
    {
        $this->datas = $datas;
    }
    public function array(): array
    {
        return   $this->datas;
    }

    public function headings(): array
    {
        $header = array();
        if (isset($this->datas[0])) {
            foreach ($this->datas[0]  as $key => $value) {
                $header[] = $key;
            }
        } else {
            $header[] = "Veri yok";
        }
        return $header;
    }
}
