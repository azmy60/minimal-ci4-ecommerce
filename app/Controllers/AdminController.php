<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AdminController extends BaseController
{
    public function home()
    {
        if(!logged_in())
            return redirect()->route('/');

        return $this->render('home');
    }

    public function products()
    {
        if(!logged_in())
            return redirect()->route('/');

        return $this->render('products');
    }

    // Only use render() for views that is wrapped in wrappers/admin
    function render($name)
    {
        $theView = "admin/$name";
        if($this->request->hasHeader('HX-Request'))
        {
            return view($theView);
        }

        $viewRenderer = \Config\Services::renderer();

        $viewRenderer->section('main');
        echo view($theView);
        $viewRenderer->endSection();

        return $viewRenderer->render('wrappers/admin');
    }
}
