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

    public function getProductCount($catId) {
        $productCats = $this->where('cat_id', $catId)->findAll();
        $productCount = count($productCats);
        return $productCount;
    }
}
