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

    function findCategories($search = '', $limit = 0, $offset = 0) {
        return $this->_search($search, '', $limit, $offset);
    }

    function findVisibles($search = '', $limit = 0, $offset = 0) {
        return $this->_search($search, 'is_visible = 1', $limit, $offset);
    }

    function findInvisibles($search = '', $limit = 0, $offset = 0) {
        return $this->_search($search, 'is_visible = 0', $limit, $offset);
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
            "name LIKE '%" . $this->db->escapeLikeString($search) .
            "%' ESCAPE '!'";
        }

        $query = $this->db->query($sql); // TODO: handle error
        return $query->getResultArray();
    }
}
