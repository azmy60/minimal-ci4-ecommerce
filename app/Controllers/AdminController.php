<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AdminController extends BaseController
{
    public function home()
    {
        return $this->render('home');
    }

    public function products()
    {
        return $this->render('products');
    }

    // Only use render() for views that is wrapped in wrappers/admin
    function render($name)
    {
        $dontExtend = $this->request->hasHeader('HX-Request');
        return $this->twig->render("admin/$name.html", [
            'template' => $dontExtend ? 'wrappers/admin_empty.html.twig' : 'wrappers/admin.html.twig',
        ]);
    }
}
