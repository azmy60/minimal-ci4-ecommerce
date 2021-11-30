<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\ProductPhotoModel;
use App\Models\StoreModel;

class AdminController extends BaseController
{
    public function home()
    {
        return $this->render('home');
    }

    public function products()
    {
        $productModel = model(ProductModel::class);
        $productPhotoModel = model(ProductPhotoModel::class);

        $products = $productModel->getLimitAll(10);
        foreach ($products as $index => $_) {
            $products[$index]['filenames'] = $productPhotoModel->getFilenames($products[$index]['id']);
        }

        $data['products'] = $products;
        $data['products_length'] = count($products);

        return $this->render('products', $data);
    }

    // public function updateStock($id = null)
    // {
    //     if($id == null)
    //         throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            
    //     $stockValue = $this->request->getRawInput()['stock'];
    //     $productModel = model(ProductModel::class);

    //     $productModel->update($id, [
    //         'stock' => $stockValue,
    //     ]);


    //     return $this->products();
    // }

    public function updateProduct($id = null)
    {
        if($id == null)
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        
        $productModel = model(ProductModel::class);
        $productModel->update($id, $this->request->getRawInput());

        return $this->products();
    }

    public function deleteProduct($id = null)
    {
        if($id == null)
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        
        $productModel = model(ProductModel::class);
        $productModel->delete($id);

        return $this->products();
    }

    public function categories()
    {
        return $this->render('categories');
    }

    public function settings()
    {
        return $this->render('settings');
    }


    public function updateStore()
    {
        $data = $this->request->getRawInput();
        // return var_dump($data);
        
        $storeModel = model(StoreModel::class);
        $id = $storeModel->first()['id'];
        // return $id;

        $storeModel->update($id, $data);

        return $this->settings();
    }

    public function help()
    {
        return $this->render('help');
    }

    public function addProduct()
    {
        return $this->twig->render('admin/add_product.html');
    }

    public function attemptAddProduct()
    {
        $photos = $this->request->getFileMultiple('photos');
        $productData = $this->request->getPost();
        
        // Photo validation
        foreach ($photos as $index => $photo) {
            if (!$photo->isValid()) {
                return redirect()->back()->withInput()->with("errors.photos", $photo->getErrorString());
            }
            if(!$this->validate(["photos.$index" => "max_size[photos.$index,2048]|mime_in[photos.$index,image/png,image/jpeg]"])) {
                return redirect()->back()->withInput()->with('errors.photos', $this->validator->getErrors());
            }
        }

        if(!$this->validate('attemptAddProduct')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $productsModel = model(ProductModel::class);
        $success = $productsModel->insert($productData);
        if(!$success) {
            return redirect()->setStatusCode(500)->back()->withInput()->with('error', 'Cannot insert a new product');
        }

        $productId = $productsModel->getInsertID();
        $productPhotoModel = model(ProductPhotoModel::class);
        foreach ($photos as $photo) {
            $productPhotoModel->store($productId, $photo);
        }

        return redirect()->route('admin/products')->with('message', 'Berhasil menambahkan produk baru');
    }

    public function onboarding()
    {
        if(!$this->store->needOnboarding()) {
            $redirectURL = session('redirect_url') ?? site_url('/admin');
			unset($_SESSION['redirect_url']);

			return redirect()->to($redirectURL);
        }

        return $this->twig->render('admin/onboarding.html', [
            'template' => 'wrappers/admin.html.twig',
        ]);
    }

    public function onboardingSetup()
    {
        $rules = [
            'name' => 'required',
            'phone' => 'required',
        ];
        
        if(!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = $this->request->getPost('name');
        $phone = $this->request->getPost('phone');
        
        if(!$this->store->onboardingSetup($name, $phone)) {
            return redirect()->back()->withInput()->with('errors', $this->store->errors());
        }

        return redirect()->route('login')->with('message', 'Yes! Tokomu sudah siap. Ayo buat produk pertamamu untuk dipajang.');
    }

    // Only use render() for views that is wrapped in wrappers/admin
    function render($name, $data = [])
    {
        $dontExtend = $this->request->hasHeader('HX-Request');
        $data['template'] = $dontExtend ? 'wrappers/admin_empty.html.twig' : 'wrappers/admin.html.twig';
        $data['store'] = model(StoreModel::class)->getStoreInfo();
        return $this->twig->render("admin/$name.html", $data);
    }
}
