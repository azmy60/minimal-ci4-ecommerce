<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AdminController extends BaseController
{
    public function home()
    {
        if(!logged_in())
            return redirect()->route('/');

        return view('admin/home');
    }

    public function products()
    {
        if(!logged_in())
            return redirect()->route('/');

        return view('admin/products');
    }
}
