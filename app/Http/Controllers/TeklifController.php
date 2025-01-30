<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeklifController extends Controller
{
    public function store(Request $request)
    {
        // Teklif verilerini kaydetme işlemleri
        return redirect()->back()->with('success', 'Teklif başarıyla eklendi.');
    }
}
