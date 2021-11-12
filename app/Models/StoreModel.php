<?php

namespace App\Models;

use CodeIgniter\Model;

class StoreModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'store';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = [
        'name', 'desc', 'address', 'phone', 'owner_name', 'owner_phone',
        'email', 'socials', 
    ];

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

    function onboardingSetup($name, $phone) {
        return $this->insert([
            'name' => $name,
            'phone' => $phone,
        ]);
    }

    function needOnboarding() {
        return $this->first() == null;
    }

}
