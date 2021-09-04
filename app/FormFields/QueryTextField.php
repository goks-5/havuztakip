<?php

namespace App\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class QueryTextField extends AbstractHandler
{
    protected $codename = 'query_text';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('formfields.query_text', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
