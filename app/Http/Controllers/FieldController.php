<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Field; // Field modelini kullanıyoruz

class FieldController extends Controller
{
    public function getFields()
    {
        try {
            $fields = Field::all(['id', 'name']); // 'fields' tablosundan 'id' ve 'name' alanlarını alıyoruz
            return response()->json($fields);
        } catch (\Exception $e) {
            // Hata durumunda log yaz ve 500 hatası döndür
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Bölüm bilgileri alınamadı.'], 500);
        }
    }
}
