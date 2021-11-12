<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StoreModel;

class AdminController extends BaseController
{
    public function home()
    {
        if($this->store->needOnboarding()) {
			return redirect()->to('admin/onboarding');
        }

        return $this->render('home');
    }

    public function products()
    {
        if($this->store->needOnboarding()) {
			return redirect()->to('admin/onboarding');
        }

        return $this->render('products');
    }

    public function categories()
    {
        if($this->store->needOnboarding()) {
			return redirect()->to('admin/onboarding');
        }

        return $this->render('categories');
    }

    public function settings()
    {
        if($this->store->needOnboarding()) {
			return redirect()->to('admin/onboarding');
        }

        return $this->render('settings');
    }

    public function help()
    {
        if($this->store->needOnboarding()) {
			return redirect()->to('admin/onboarding');
        }

        return $this->render('help');
    }

    public function addProduct()
    {
        if($this->store->needOnboarding()) {
			return redirect()->to('admin/onboarding');
        }

        return $this->twig->render('admin/add_product.html');
    }

    public function onboarding()
    {
        if(!$this->store->needOnboarding()) {
            $redirectURL = session('redirect_url') ?? site_url('/admin');
			unset($_SESSION['redirect_url']);

			return redirect()->to($redirectURL);
        }

        return $this->twig->render('admin/onboarding.html', [
            'template' => 'wrappers/admin.html.twig',
        ]);
    }

    public function onboardingSetup()
    {
        $rules = [
            'name' => 'required',
            'phone' => 'required',
        ];
        
        if(!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = $this->request->getPost('name');
        $phone = $this->request->getPost('phone');
        
        if(!$this->store->onboardingSetup($name, $phone)) {
            return redirect()->back()->withInput()->with('errors', $this->store->errors());
        }

        return redirect()->route('login')->with('message', 'Yes! Tokomu sudah siap. Ayo buat produk pertamamu untuk dipajang.');
    }

    // Only use render() for views that is wrapped in wrappers/admin
    function render($name)
    {
        $dontExtend = $this->request->hasHeader('HX-Request');
        return $this->twig->render("admin/$name.html", [
            'template' => $dontExtend ? 'wrappers/admin_empty.html.twig' : 'wrappers/admin.html.twig',
        ]);
    }
}
