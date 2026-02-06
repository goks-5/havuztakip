<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserAccount; 
use App\Firm;

class UserController extends Controller
{
    public function index(Request $request)
{
    $search = $request->get('search');

    $users = UserAccount::where(function ($q) use ($search) {
            if ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            }
        })
        ->orderBy('id', 'desc')
        ->paginate(10); // ✅ SADECE paginate

    $companies = Firm::all(['company_name']);

    return view(
        'vendor.voyager.kullanici-tablosu.browse',
        compact('users', 'companies', 'search')
    );
}

    public function store(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email',
            'telephone' => 'required|string|max:15',
        ]);

        UserAccount::create([
            'user_name' => $request->user_name,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'telephone' => $request->telephone,
        ]);

        return redirect()
            ->route('kullanici-tablosu.index')
            ->with('success', 'Kullanıcı başarıyla eklendi.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email,' . $id,
            'telephone' => 'required|string|max:15',
        ]);

        $user = UserAccount::findOrFail($id);
        $user->update($request->only([
            'user_name',
            'company_name',
            'email',
            'telephone'
        ]));

        return redirect()
            ->route('kullanici-tablosu.index')
            ->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    public function destroy(Request $request, $id)
    {
        UserAccount::findOrFail($id)->delete();

        return redirect()
            ->route('kullanici-tablosu.index')
            ->with('success', 'Kullanıcı başarıyla silindi.');
    }
}
