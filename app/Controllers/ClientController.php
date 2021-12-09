<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductCategoryModel;
use App\Models\ProductModel;
use App\Models\ProductPhotoModel;

class ClientController extends BaseController
{
    public function index()
    {
        $productModel = model(ProductModel::class);
        $productPhotoModel = model(ProductPhotoModel::class);

        $products = $productModel->getAll();
        foreach ($products as $index => $_) {
            $products[$index]['filenames'] = $productPhotoModel->getFilenames($products[$index]['id']);
        }

        $data['products'] = $products;
        $data['products_length'] = count($products);

        return $this->render('home', $data);
    }
    
    public function product($title)
    {
        $productModel = model(ProductModel::class);
        $product = $productModel->where('title', $title)->first();
        if(!$product)
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    
        $productPhotoModel = model(ProductPhotoModel::class);
        $photos = $productPhotoModel->getFilenames($product['id']);

        $data = [
            'product' => $product,
            'photos' => $photos,
        ];

        return $this->render('product', $data);
    }

    public function category($name)
    {
        $categoryModel = model(CategoryModel::class);
        $category = $categoryModel->where('name', $name)->first();
        if(!$category)
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        
        $productCategoryModel = model(ProductCategoryModel::class);
        $products = $productCategoryModel->getProducts($category['id']);
        if(!$products)
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        
        $data = [
            'products' => $products,
            'category' => $category,
        ];

        return $this->render('category', $data);
    }
    
    function render($name, $data = [])
    {
        $dontExtend = $this->request->hasHeader('HX-Request');
        
        $store = model(StoreModel::class)->getStoreInfo();
        if(!$store)
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $categoryModel = model(CategoryModel::class);
        $categories = $categoryModel->findAll();

        // Show the website only if admin asks to and product table is not empty
        $showStore = $store['status'] == 1 && !empty(model(ProductModel::class)->first());

        $data['categories'] = $categories;
        $data['template'] = $dontExtend ? 'wrappers/client_empty.html.twig' : 'wrappers/client.html.twig';
        $data['store'] = model(StoreModel::class)->getStoreInfo();
        $data['show_store'] = $showStore;
        
        return $this->twig->render("client/$name.html", $data);
    }
}
