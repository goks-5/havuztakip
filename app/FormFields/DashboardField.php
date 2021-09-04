<?php

namespace App\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class DashboardField extends AbstractHandler
{
    protected $codename = 'dashboard';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('formfields.dashboard', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
