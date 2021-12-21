<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductCategoryModel;
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
        $categoryModel = model(CategoryModel::class);

        $products = $productModel->getAll();
        foreach ($products as $index => $_) {
            $thumbnails = [];
            $filenames = $productPhotoModel->getFilenames($products[$index]['id']);
            foreach ($filenames as $filename) {
                array_push($thumbnails, [
                    'id' => $filename['id'],
                    'src' => route_to('content-photos', 'sm', $filename['filename']),
                    'order' => $filename['sort_order'],
                ]);
            }
            
            $products[$index]['thumbnails'] = $thumbnails;
            $products[$index]['categories'] = $productModel->getCats($products[$index]['id']);
        }

        $inStocks = $productModel->findInStocks();
        $outOfStocks = $productModel->findOutOfStocks();

        $categories = $categoryModel->findAll();

        $data['products'] = $products;
        $data['products_count'] = count($products);
        $data['in_stocks_count'] = count($inStocks);
        $data['out_of_stocks_count'] = count($outOfStocks);
        $data['categories'] = $categories;

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

    public function findProducts()
    {
        $productModel = model(ProductModel::class);
        $productPhotoModel = model(ProductPhotoModel::class);
        $categoryModel = model(CategoryModel::class);
        $search = $this->request->getGet('q');
        $filter = $this->request->getGet('filter');
        $products = [];

        if($filter == 'all') {
            $products = $productModel->findProducts($search);
        } else if($filter == 'inStocks') {
            $products = $productModel->findInStocks($search);
        } else if($filter == 'outOfStocks') {
            $products = $productModel->findOutOfStocks($search);
        }

        foreach ($products as $index => $_) {
            $products[$index]['filenames'] = $productPhotoModel->getFilenames($products[$index]['id']);
            $products[$index]['categories'] = $productModel->getCats($products[$index]['id']);
        }

        $categories = $categoryModel->findAll();
        $data = [
            'products' => $products,
            'categories' => $categories,
        ];

        return $this->render('product_list_items', $data);
    }

    public function updateProduct($id = null)
    {
        if($id == null)
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        
        $productData = $this->request->getRawInput();
        
        $productModel = model(ProductModel::class);
        $productModel->update($id, $productData);

        $cats = json_decode($productData['cats']);
        if($cats != null)
            $productModel->updateCats($id, $cats);

        return $this->products();
    }

    public function updatePhotos()
    {
        $photos = $this->request->getFileMultiple('photos') ?? [];
        $photosData = $this->request->getPost();
        $productId = $photosData['product_id'];
        $photosOrders = json_decode($photosData['photos_orders'], true);
        $dbOrders = json_decode($photosData['db_orders'], true);
        $deleteIds = json_decode($photosData['delete_ids'], true);

        // echo '<pre>'.var_export($photos,true).'</pre>';
        // echo '<pre>'.var_export($photosOrders,true).'</pre>';
        // echo '<pre>'.var_export($dbOrders,true).'</pre>';
        // return '<pre>'.var_export($deleteIds,true).'</pre>';
        
        $productPhotoModel = model(ProductPhotoModel::class);

        // Photo validation
        foreach ($photos as $index => $photo) {
            if (!$photo->isValid()) {
                return redirect()->back()->withInput()->with("errors.photos", $photo->getErrorString());
            }
            if(!$this->validate(["photos.$index" => "max_size[photos.$index,2048]|mime_in[photos.$index,image/png,image/jpeg]"])) {
                return redirect()->back()->withInput()->with('errors.photos', $this->validator->getErrors());
            }

            // $newOrders = explode(',', $photosData['new_orders']);
            $productPhotoModel->store($productId, $photo, $photosOrders[$index] ?? null);
        }

        // $orders = explode(',', $photosData['old_orders']);
        if(!empty($dbOrders)) $productPhotoModel->setOrders($productId, $dbOrders);

        if(!empty($deleteIds)) $productPhotoModel->delete($deleteIds);

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

    public function deleteProducts()
    {
        $data = $this->request->getRawInput();
        $ids = json_decode($data['ids'], true);

        if(empty($ids)) return $this->products();

        model(ProductModel::class)->delete($ids);

        return $this->products();
    }

    public function categories()
    {
        $categoryModel = model(CategoryModel::class);
        $productCategoryModel = model(ProductCategoryModel::class);

        $products = model(ProductModel::class)->findAll();
        foreach ($products as $index => $_) {
            $products[$index]['filenames'] = model(ProductPhotoModel::class)->getFilenames($products[$index]['id']);
        }

        $categories = $categoryModel->findAll();
        foreach ($categories as $index => $category) {
            $id = $category['id'];
            $selectedProducts = \Config\Database::connect()
                ->query("
                    SELECT
                        product_categories.product_id,
                        products.title,
                        product_photos.filename
                    FROM product_categories
                    LEFT JOIN (
                        SELECT filename, MIN(product_id) product_id
                        FROM product_photos
                        GROUP BY product_id
                    ) product_photos ON product_categories.product_id = product_photos.product_id
                    LEFT JOIN products ON product_categories.product_id = products.id
                    WHERE product_categories.cat_id = $id
                ")
                ->getResultArray();

            $selectedIds = array_map(fn($product) => $product['product_id'], $selectedProducts);
            $unselectedProducts = array_filter($products, fn($product) => !in_array($product['id'], $selectedIds));

            $categories[$index]['selected_products'] = $selectedProducts;
            $categories[$index]['unselected_products'] = $unselectedProducts;

            $productCount = $productCategoryModel->getProductCount($id);
            $categories[$index]['product_count'] = $productCount;
        }

        $visibles = $categoryModel->findVisibles();
        $invisibles = $categoryModel->findInvisibles();

        $data['products'] = $products;
        $data['categories'] = $categories;
        $data['categories_count'] = count($categories);
        $data['visibles_count'] = count($visibles);
        $data['invisibles_count'] = count($invisibles);

        return $this->render('categories', $data);
    }

    public function findCategories()
    {
        $categoryModel = model(CategoryModel::class);
        $productCategoryModel = model(ProductCategoryModel::class);
        $search = $this->request->getGet('q');
        $filter = $this->request->getGet('filter');
        $categories = [];

        if($filter == 'all') {
            $categories = $categoryModel->findCategories($search);
        } else if($filter == 'visibles') {
            $categories = $categoryModel->findVisibles($search);
        } else if($filter == 'invisibles') {
            $categories = $categoryModel->findInvisibles($search);
        }

        foreach ($categories as $index => $category) {
            $id = $category['id'];
            $filename = $productCategoryModel->getPhotoFilename($id);
            $categories[$index]['filename'] = $filename;

            $productCount = $productCategoryModel->getProductCount($id);
            $categories[$index]['product_count'] = $productCount;
        }

        $data = [
            'categories' => $categories,
        ];

        return $this->render('category_list_items', $data);
    }

    public function updateCategory($catId) {
        $data = $this->request->getRawInput();
        $name = $data['name'];
        $productIds = $data['productIds'] ?? [];

        model(CategoryModel::class)->update($catId, [
            'name' => $name,
        ]);
        
        $productCategoryModel = model(ProductCategoryModel::class);
        if(!empty($productIds)) {
            $productCategoryModel->where('cat_id', $catId)->whereNotIn('product_id', $productIds)->delete();
            
            foreach ($productIds as $productId) {
                if($productCategoryModel
                    ->where('cat_id', $catId)
                    ->where('product_id', $productId)
                    ->countAllResults() == 0) {
                    $productCategoryModel->insert([
                        'cat_id' => $catId,
                        'product_id' => $productId,
                    ]);
                }
            }            
        } else {
            $productCategoryModel->where('cat_id', $catId)->delete();
        }

        return $this->categories();
    }

    public function deleteCategories()
    {
        $data = $this->request->getRawInput();
        $ids = json_decode($data['ids'], true);

        if(empty($ids)) return $this->categories();

        model(ProductCategoryModel::class)->whereIn('cat_id', $ids)->delete();
        model(CategoryModel::class)->delete($ids);

        return $this->categories();
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
        $categoryModel = model(CategoryModel::class);
        $categories = $categoryModel->findAll();

        // TODO: send id and name of categories only
        $data = [
            'categories' => $categories, 
        ];

        return $this->twig->render('admin/add_product.html', $data);
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

        $cats = json_decode($productData['cats']);
        if($cats != null)
            $productsModel->updateCats($productId, $cats);

        return redirect()->route('admin/products')->with('message', 'Berhasil menambahkan produk baru');
    }

    public function addCategory()
    {
        $storeModel = model(StoreModel::class);
        $productModel = model(ProductModel::class);
        $productPhotoModel = model(ProductPhotoModel::class);
        
        $store = $storeModel->first();
        $products = $productModel->findAll();

        foreach ($products as $index => $_) {
            $products[$index]['filenames'] = $productPhotoModel->getFilenames($products[$index]['id']);
        }
        
        $data = [
            'products' => $products,
            'store' => $store,
        ];
        
        return $this->twig->render('admin/add_category.html', $data);
    }

    public function attemptAddCategory()
    {
        if(!$this->validate('attemptAddCategory')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $productIds = null;
        if(array_key_exists('productIds', $data)) {
            $productIds = $data['productIds'];
            unset($data['products']);
        }

        $categoryModel = model(CategoryModel::class);
        $success = $categoryModel->insert($data);
        if(!$success) {
            return redirect()->setStatusCode(500)->back()->withInput()->with('error', 'Cannot insert a new category');
        }

        if($productIds != null) {
            $catId = $categoryModel->getInsertID();
            $productCategoryModel = model(ProductCategoryModel::class);
            foreach ($productIds as $productId) {
                $productCategoryModel->insert([
                    'product_id' => $productId,
                    'cat_id' => $catId,
                ]);
            }
        }

        return $this->categories();
    }

    public function deleteCategory($id = null)
    {
        if($id == null)
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        
        $productCategoryModel = model(ProductCategoryModel::class);
        $categoryModel = model(CategoryModel::class);

        $productCategoryModel->where('cat_id', $id)->delete();
        $categoryModel->delete($id);

        return $this->categories();
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
