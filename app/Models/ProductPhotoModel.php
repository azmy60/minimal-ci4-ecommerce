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
    protected $allowedFields        = ['product_id', 'sort_order', 'filename'];

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
     * $files and $orders should always be of equals length 
     * 
     * @param int $productId
     * @param CodeIgniter\HTTP\Files\UploadedFile[] $files
     * @param mixed[] $orders
     *
     * @return boolean
     */
    public function store($productId, $files, $orders)
    {
        $insertValues = array_map(function($file, $order) use ($productId){
            $filename = $file->getRandomName();
            $file->store('product-photos', $filename);
            return [
                'product_id' => $productId,
                'sort_order' => $order,
                'filename' => $filename,
            ];
        }, $files, $orders);

        return $this->insertBatch($insertValues);
    }

    /**
     * @param int $productId
     * 
     * @return array
     */
    public function getFilenames($productId)
    {
        return $this->where('product_id', $productId)->orderBy('sort_order', 'asc')->findAll();
    }

    public function getProductsFilenames($productIds)
    {
        $productsFilenames = [];
        foreach ($productIds as $productId) {
            $productsFilenames['product_id'] = $this->getFilenames($productId);
        }
        return $productsFilenames;
    }

    public function setOrders($productId, $orders) {
        // $ids = $this->where('product_id', $productId)->findColumn('id');
        // echo '<pre>'.var_export($orders,true).'</pre>';
        $batch = array_map(fn($order) => [
            'id' => $order['id'],
            'sort_order' => $order['order'],
        ], $orders);
        // return $batch;
        return $this->updateBatch($batch, 'id');
    }
}
