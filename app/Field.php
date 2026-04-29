<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Field extends Model
{
    public $table = "fields";
    protected $codename = 'multiple_text';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        $fields = DB::table('fields')->pluck('name')->toArray(); // Veritabanından dinamik veri çekme
        return view('formfields.multiple_text', compact('row', 'dataType', 'dataTypeContent', 'options', 'fields'));
    }
}
