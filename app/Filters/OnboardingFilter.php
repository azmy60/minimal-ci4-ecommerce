<?php

namespace App\Filters;

use App\Models\StoreModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class OnboardingFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $store = model(StoreModel::class);
        if(!$store->needOnboarding())
            return;

        if ($request->hasHeader('HX-Request'))
            return redirect()->setHeader('HX-Redirect', route_to('admin/onboarding'));
        
        return redirect()->to('admin/onboarding');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {

    }
}
