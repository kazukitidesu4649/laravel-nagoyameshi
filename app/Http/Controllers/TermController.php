<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Term;

class TermController extends Controller
{
    public function __construct() {
        if (auth('admin')->check()) {
            return redirect()->route('admin.home');
        }
    }

    public function index()
    {
        $term = Term::first();
        return view('term.index', compact('term'));
    }
}
