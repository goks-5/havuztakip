<?php

namespace App\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class MultipleTextField extends AbstractHandler
{
    protected $codename = 'multiple_text';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('formfields.multiple_text', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
