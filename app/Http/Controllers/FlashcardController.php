<?php

namespace App\Http\Controllers;

use App\Models\Flashcard;
use Illuminate\Http\Request;

class FlashcardController extends Controller
{
    public function index() {
        return Flashcard::all();
    }

    public function store(Request $request) {
        $request->validate(['question' => 'required', 'answer' => 'required', 'set_id' => 'required']);
        return Flashcard::create($request->all());
    }

    public function hide($id) {
        $card = Flashcard::findOrFail($id);
        $card->is_hidden = true;
        $card->save();
        return response()->json(['message' => 'Flashcard hidden']);
    }
}
