<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserAccount; 
use App\Firm;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = UserAccount::all();
        $companies = Firm::all(['company_name']);
        return view('vendor.voyager.kullanici-tablosu.browse', compact('users', 'companies'));
    }

    public function store(Request $request)
    {
        // **Validation: Artık 'password' yok!**
        $request->validate([
            'user_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email',
            'telephone' => 'required|string|max:15',
        ]);

        // **Form verilerini ekrana yazdır (Debug için)**
        \Log::info('Gelen Form Verileri:', $request->all());

        // **Kullanıcıyı kaydet**
        UserAccount::create([
            'user_name' => $request->user_name,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'telephone' => $request->telephone,
        ]);

        return redirect()->route('kullanici-tablosu.index')->with('success', 'Kullanıcı başarıyla eklendi.');
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
        $user->update($request->all());

        return redirect()->route('kullanici-tablosu.index')->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    public function destroy(Request $request, $id)
    {
        $user = UserAccount::findOrFail($id);
        $user->delete();

        return redirect()->route('kullanici-tablosu.index')->with('success', 'Kullanıcı başarıyla silindi.');
    }
}
