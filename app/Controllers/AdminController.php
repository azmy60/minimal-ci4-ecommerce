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

    public function categories()
    {
        return $this->render('categories');
    }

    public function settings()
    {
        return $this->render('settings');
    }

    public function help()
    {
        return $this->render('help');
    }

    public function addProduct()
    {
        return $this->twig->render('admin/add_product.html');
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
