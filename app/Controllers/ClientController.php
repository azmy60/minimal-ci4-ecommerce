<?php

namespace App\Controllers;

class ClientController extends BaseController
{
    public function index()
    {
        $productModel = model(ProductModel::class);
        $productPhotoModel = model(ProductPhotoModel::class);

        $products = $productModel->getLimitAll(10);
        foreach ($products as $index => $_) {
            $products[$index]['filenames'] = $productPhotoModel->getFilenames($products[$index]['id']);
        }

        $data['products'] = $products;
        $data['products_length'] = count($products);

        return $this->render('home', $data);
    }
    
    function render($name, $data = [])
    {
        $dontExtend = $this->request->hasHeader('HX-Request');
        $data['template'] = $dontExtend ? 'wrappers/client_empty.html.twig' : 'wrappers/client.html.twig';
        return $this->twig->render("client/$name.html", $data);
    }
}
