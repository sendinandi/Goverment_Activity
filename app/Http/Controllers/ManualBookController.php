<?php

namespace App\Http\Controllers;

class ManualBookController extends Controller
{
    public function index()
    {
        return view('manual-book.index');
    }

    public function download()
    {
        $path = public_path('docs/manual-book-sipda.pdf');

        if (!file_exists($path)) {
            abort(404, 'Manual book belum tersedia.');
        }

        return response()->download($path, 'Manual_Book_SIPDA.pdf');
    }
}