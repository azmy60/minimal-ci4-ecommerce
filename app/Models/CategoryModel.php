<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'categories';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['name', 'is_visible'];

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

    function findCategories($search, $limit = 0, $offset = 0) {
        if(empty($search))
            return $this->findAll($limit, $offset);
        
        $sql = "SELECT * FROM $this->table WHERE name LIKE '%" .
        $this->db->escapeLikeString($search) . "%' ESCAPE '!'";
        
        $query = $this->db->query($sql); // TODO: handle error
        return $query->getResultArray();
    }
}
