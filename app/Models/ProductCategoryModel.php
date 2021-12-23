<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductCategoryModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'product_categories';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['product_id', 'cat_id'];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function getPhotoFilename($catId) {
        $productCat = $this->where('cat_id', $catId)->first();
        if(!$productCat)
            return '';
        
        $productId = $productCat['product_id'];

        $productPhotoModel = model(ProductPhotoModel::class);
        $filenames = $productPhotoModel->getFilenames($productId);
        
        return $filenames[0]['filename'];
    }

    public function getProducts($catId) {
        $product_ids = $this->where('cat_id', $catId)->findColumn('product_id');
        if(!$product_ids)
            return [];
        
        $productModel = model(ProductModel::class);
        $products = $productModel->find($product_ids);

        $productPhotoModel = model(ProductPhotoModel::class);
        foreach ($products as $index => $product) {
            $products[$index]['filenames'] = $productPhotoModel->getFilenames($product['id']);
        }

        return $products;
    }

    public function deleteProductCat($productId, $catId) {
        $productCat = $this->getProductCat($productId, $catId);
        if(!empty($productCat))
            return $this->delete($productCat[0]['id']);
        return false;
    }

    public function getProductCat($productId, $catId) {
        return $this->where('product_id', $productId)->where('cat_id', $catId)->find();
    }
}
