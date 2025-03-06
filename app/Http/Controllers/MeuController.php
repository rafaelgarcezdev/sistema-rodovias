<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class MeuController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Trechos/Create', [
            'page' => 'Conteúdo da página'
        ]);

        
    }

    
}
