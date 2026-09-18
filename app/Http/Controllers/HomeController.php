<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Oferta;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $produtos = Produto::with('categoria')->latest()->take(18)->get();
        $ofertas = Oferta::with('produto')->latest()->take(6)->get();

        return view('site.home', compact('produtos', 'ofertas'));
    }
}