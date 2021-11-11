<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect ;

class RedirectController extends Controller
{
    
    public function redirectRoot(Request $request)
    {return Redirect::to('/', 301);
    }
}
