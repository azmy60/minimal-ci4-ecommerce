<?php

namespace App\Controllers;

use App\Models\StoreModel;
use App\Models\CategoryModel;
use App\Models\ProductCategoryModel;
use App\Models\ProductModel;
use App\Models\ProductPhotoModel;

class ClientController extends BaseController
{
    public function index()
    {
        $data['products'] = model(ProductModel::class)->getProductsWithOneFilename();
        return $this->render('home', $data);
    }
    
    public function product($title)
    {
        $product = model(ProductModel::class)->where('title', $title)->first();
        if(!$product)
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    
        $photos = model(ProductPhotoModel::class)->getFilenames($product['id']);

        $data = [
            'product' => $product,
            'photos' => $photos,
        ];

        return $this->render('product', $data);
    }

    public function category($name)
    {
        $products = model(ProductModel::class)->getProductsByCategoryName($name);
        
        $data = [
            'products' => $products,
            'category_name' => $name,
        ];

        return $this->render('category', $data);
    }
    
    function render($name, $data = [])
    {
        $dontExtend = $this->request->hasHeader('HX-Request');
        
        $storeModel = model(StoreModel::class);

        $store = $storeModel->getStoreInfo();
        if(!$store)
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Toko ini tidak tersedia.');

        $categories = model(CategoryModel::class)->findAll();

        // Show the website only if admin asks to and product table is not empty
        $showStore = $store['status'] == 1 && !empty($storeModel->first());

        $data['categories'] = $categories;
        $data['template'] = $dontExtend ? 'wrappers/client_empty.html.twig' : 'wrappers/client.html.twig';
        $data['store'] = $store;
        $data['show_store'] = $showStore;
        
        return $this->twig->render("client/$name.html", $data);
    }
}
