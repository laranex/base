<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use laranex\LaravelMyanmarNRC\Rules\MyanmarNRC;

class TestController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'nrc' => ['required', 'string', new MyanmarNRC()],
        ]);

        return $request->nrc;
    }
}
