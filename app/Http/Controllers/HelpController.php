<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HelpController extends Controller
{
    /**
     * Display help & support page.
     */
    public function index(): View
    {
        $user = auth()->user();
        return view('help.index', compact('user'));
    }
}
