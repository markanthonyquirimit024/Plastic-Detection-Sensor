<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FirebaseController extends Controller
{
    public function write()
    {
        $db = app('firebase.database');

        $db->getReference('messages')->push([
            'user' => 'Mark',
            'message' => 'Hello Firebase 👋',
            'time' => now()->toDateTimeString(),
        ]);

        return response()->json(['status' => 'message saved']);
    }

    public function read()
    {
        $db = app('firebase.database');

        $messages = $db->getReference('messages')->getValue();

        return response()->json($messages);
    }
}
