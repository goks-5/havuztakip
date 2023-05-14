<?php

namespace App\Http\Controllers;

use App\Http\Controllers\VoyagerBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Trigers extends VoyagerBaseController
{

    public function store(Request $request)
    {
     
        return parent::store($request);
    }

    public function update(Request $request, $id)
    {
        return parent::update($request, $id);
    }

    public function insertUpdateData($request, $slug, $rows, $data)
    {
      
        return parent::insertUpdateData($request, $slug, $rows, $data);
    }
}
