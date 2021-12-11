<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'products';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = true;
    protected $protectFields        = true;
    protected $allowedFields        = ['title', 'desc', 'price', 'stock'];

    // Dates
    protected $useTimestamps        = true;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * @param int $limit
     * @param int $offset
     *  
     * @return array
     */
    function getAll($limit = 0, $offset = 0) {
        return $this->findAll($limit, $offset);
    }

    function getCats($id) {
        $productCats = model(ProductCategoryModel::class)->getProductCatsByCatId($id);
        if(empty($productCats))
            return [];
        
        return model(CategoryModel::class)->find($productCats);;
    }

    function updateCats($id, $cats) {
        $categoryModel = model(CategoryModel::class);
        $productCategoryModel = model(ProductCategoryModel::class);
        foreach ($cats as $_ => $cat) {
            $catId = $cat->id;
            if($catId < 0) {
                $catId = $categoryModel->insert([
                    'name' => $cat->name,
                ]);
            }
            if(!$productCategoryModel->getProductCat($id, $catId)) {
                $productCategoryModel->insert([
                    'product_id' => $id,
                    'cat_id' => $catId,
                ]);
            } else if(property_exists($cat, 'remove') && $cat->remove > 0) {
                $productCategoryModel->deleteProductCat($id, $catId);
            }
        }
    }

    function findProducts($search = '', $limit = 0, $offset = 0) {
        return $this->_search($search, '', $limit, $offset);
    }

    function findInStocks($search = '', $limit = 0, $offset = 0) {
        return $this->_search($search, 'stock = 1', $limit, $offset);
    }

    function findOutOfStocks($search = '', $limit = 0, $offset = 0) {
        return $this->_search($search, 'stock = 0', $limit, $offset);
    }

    function _search($search = '', $where = '', $limit = 0, $offset = 0) {
        $limit_q = $limit ? "LIMIT $limit" : '';
        $offset_q = $offset ? "OFFSET $offset" : '';
        $sql = "SELECT * FROM $this->table $limit_q $offset_q";
        $query = null;
        
        if(empty($search)) {
            $sql .= !empty($where) ? "WHERE $where" : '';
        } else {
            $sql .= 'WHERE ' . (!empty($where) ? "$where AND " : '') .
            "title LIKE '%" . $this->db->escapeLikeString($search) .
            "%' ESCAPE '!'";
        }

        $query = $this->db->query($sql); // TODO: handle error
        return $query->getResultArray();
    }
}
