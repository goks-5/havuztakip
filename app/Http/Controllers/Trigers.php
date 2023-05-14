<?php

namespace App\Http\Controllers;

use App\Http\Controllers\VoyagerBaseController;
use Illuminate\Http\Request;

class Trigers extends VoyagerBaseController
{

    public function store(Request $request)
    {
        $request->merge(['users' => json_encode($request->input('users'))]);
        return parent::store($request);
    }

    public function update(Request $request, $id)
    {
        $request->merge(['users' => json_encode($request->input('users'))]);
        return parent::update($request, $id);
    }
}
