<?php

namespace App\Http\Controllers;

use App\Http\Controllers\VoyagerBaseController;
use Illuminate\Support\Facades\Storage;

class Trigers extends VoyagerBaseController
{
    public function insertUpdateData($request, $slug, $rows, $data)
    {
        dd($request);
        return parent::insertUpdateData($request, $slug, $rows, $data) ;
    }
}
