<?php namespace App\Controllers;

class Inventory extends BaseController
{
    public function index()
    {
        return view('inventory', ['title' => 'Inventory']);
    }
}

