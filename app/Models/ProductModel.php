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

    function getProductsByCategoryName($name) {
        return $this->db->query("
        SELECT
            products.*,
            product_photos.filename
        FROM products
        LEFT JOIN categories ON (
            categories.name = '$name'
            AND categories.deleted_at IS NULL    
        )
        LEFT JOIN product_categories ON product_categories.cat_id = categories.id
        LEFT JOIN product_photos ON (
            product_photos.product_id = products.id
            AND product_photos.sort_order = (
                SELECT MIN(sort_order)
                FROM product_photos
                WHERE product_id = products.id
            )
        )
        WHERE
            products.id = product_categories.product_id
            AND products.deleted_at IS NULL
        ")->getResultArray();
    }

    function getProductsWithOneFilename() {
        return $this->db->query('
            SELECT
                products.*,
                product_photos.filename
            FROM products
            LEFT JOIN product_photos ON (
                product_photos.product_id = products.id
                AND product_photos.sort_order = (
                    SELECT MIN(sort_order)
                    FROM product_photos
                    WHERE product_id = products.id
                )
            )
            WHERE products.deleted_at IS NULL
        ')
        ->getResultArray();
    }

    function getProductsWithFilenames() {
        $products = $this->db->query('
            SELECT
                products.*,
                GROUP_CONCAT(product_photos.filename) as filenames
            FROM products
            LEFT JOIN (
                SELECT filename, product_id
                FROM product_photos
                ORDER BY sort_order ASC
            ) product_photos ON product_photos.product_id = products.id
            WHERE products.deleted_at IS NULL
            GROUP BY products.id
        ')
        ->getResultArray();

        foreach ($products as $k => $product) {
            $products[$k]['filenames'] = explode(',', $product['filenames']); 
        }
        
        return $products;
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

    function countInStocks() {
        return $this->_search('', 'stock = 1', 0, 0, true)[0]['COUNT(*)'];
    }

    function countOutOfStocks() {
        return $this->_search('', 'stock = 0', 0, 0, true)[0]['COUNT(*)'];
    }

    function _search($search = '', $where = '', $limit = 0, $offset = 0, $justCount = false) {
        $limit_q = $limit ? "LIMIT $limit" : '';
        $offset_q = $offset ? "OFFSET $offset" : '';
        $select = $justCount ? "COUNT(*)" : '*';
        $sql = "SELECT $select FROM $this->table $limit_q $offset_q WHERE deleted_at is NULL ";
        $query = null;
        
        if(empty($search)) {
            $sql .= !empty($where) ? "AND $where" : '';
        } else {
            $sql .= 'AND ' . (!empty($where) ? "$where AND " : '') .
            "title LIKE '%" . $this->db->escapeLikeString($search) .
            "%' ESCAPE '!'";
        }

        $query = $this->db->query($sql); // TODO: handle error
        return $query->getResultArray();
    }
}
