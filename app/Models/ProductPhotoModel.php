<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductPhotoModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'product_photos';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['product_id', 'filename'];

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
    
    /**
     * @param int $productId
     * @param CodeIgniter\HTTP\Files\UploadedFile $photoFile
     *
     * @return boolean
     */
    public function store($productId, $photoFile)
    {
        $filename = $photoFile->getRandomName();
        $path = $photoFile->store('product-photos', $filename);
        return $this->insert([
            'product_id' => $productId,
            'filename' => $filename,
        ]);
    }

    /**
     * @param int $productId
     * 
     * @return array
     */
    public function getFilenames($productId)
    {
        return $this->select('filename')->where('product_id', $productId)->get()->getResultArray();
    }
}
