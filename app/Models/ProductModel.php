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

    function findProducts($search, $limit = 0, $offset = 0) {
        if(empty($search))
            return $this->findAll($limit, $offset);
        
        $sql = "SELECT * FROM $this->table WHERE title LIKE '%" .
        $this->db->escapeLikeString($search) . "%' ESCAPE '!'";
        
        $query = $this->db->query($sql); // TODO: handle error
        return $query->getResultArray();
    }
}
