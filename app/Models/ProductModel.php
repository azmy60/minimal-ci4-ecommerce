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
    protected $allowedFields        = ['title', 'desc', 'price', 'stock', 'cat_id'];

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
     * Returns -1 on failure 
     * @return int
     */
    function add($title, $desc, $price, $stock, $catId)
    {
        $success = $this->insert([
            'title' => $title,
            'desc' => $desc,
            'price' => $price,
            'stock' => $stock,
            'cat_id' => $catId,
        ]);
        return $success ? $this->getInsertID() : -1;
    }
}
