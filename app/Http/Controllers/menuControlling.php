<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class menuControlling extends Controller
{
    public function index() 
    {
        return view('admin.setting.menu');
    }
}
