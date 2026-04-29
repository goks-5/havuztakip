<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Firm;

class CompanyController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'telephone' => 'required|string|max:15',
        ]);

        Firm::create($request->only(['company_name', 'address', 'telephone']));

        return redirect()->route('firma-tablosu.browse')->with('success', 'Firma başarıyla eklendi.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'telephone' => 'required|string|max:15',
        ]);

        $company = Firm::findOrFail($id);
        $company->update($request->only(['company_name', 'address', 'telephone']));

        return redirect()->route('firma-tablosu.browse')->with('success', 'Firma başarıyla güncellendi.');
    }

    public function destroy(Request $request, $id)
    {
        $company = Firm::findOrFail($id);
        $company->delete();

        return redirect()->route('firma-tablosu.browse')->with('success', 'Firma başarıyla silindi.');
    }

}
