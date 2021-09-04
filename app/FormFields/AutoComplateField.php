<?php

namespace App\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class AutoComplateField extends AbstractHandler
{
    protected $codename = 'auto_complate';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('formfields.auto_complate', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
