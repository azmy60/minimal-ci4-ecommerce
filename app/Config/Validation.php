<?php

namespace Config;

use CodeIgniter\Validation\CreditCardRules;
use CodeIgniter\Validation\FileRules;
use CodeIgniter\Validation\FormatRules;
use CodeIgniter\Validation\Rules;

class Validation
{
    //--------------------------------------------------------------------
    // Setup
    //--------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
        \Myth\Auth\Authentication\Passwords\ValidationRules::class
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    //--------------------------------------------------------------------
    // Rules
    //--------------------------------------------------------------------

    public $attemptReset = [
        'token'        => 'required',
        'email'        => 'required|valid_email',
        'password'     => 'required|strong_password',
        'pass_confirm' => [
            'rules' => 'required|matches[password]',
            'errors' => [
                'matches' => 'Please make sure your password match.',
            ],
        ],
    ];

    public $attemptAddProduct = [
        'title'         => 'required|max_length[80]',
        'desc'          => 'required|max_length[4000]',
        'price'         => 'required|numeric',
        'stock'         => 'required|in_list[0,1]',
    ];
    
    public $attemptAddCategory = [
        'name'          => 'required|max_length[80]',
        'desc'          => 'max_length[4000]',
        'productIds.*'    => 'permit_empty|integer',
        'is_visible'    => 'required|in_list[0,1]',
    ];
}
