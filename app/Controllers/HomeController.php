<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;

class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('pages/home', [
            'title' => 'Welcome',
            'message' => 'Wisp is ready — a barely-there PHP micro-framework.',

        ]);
    }

    public function about(Request $request): Response
    {
        return $this->view('pages/about', [
            'title' => 'About',
        ]);
    }
}
