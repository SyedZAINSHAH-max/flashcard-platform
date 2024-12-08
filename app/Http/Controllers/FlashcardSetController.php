<?php

namespace App\Http\Controllers;

use App\Models\FlashcardSet;
use Illuminate\Http\Request;

class FlashcardSetController extends Controller
{
    public function index() {
        return FlashcardSet::all();
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required', 'user_id' => 'required']);
        return FlashcardSet::create($request->all());
    }

    public function rate(Request $request, $id) {
        $set = FlashcardSet::findOrFail($id);
        $set->rating = $request->input('rating', $set->rating);
        $set->save();
        return response()->json($set);
    }
}
