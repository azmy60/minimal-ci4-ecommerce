<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Images\Exceptions\ImageException;

class ContentController extends BaseController
{
  // photos/<xsmall>/<filename>
  public function photos($size = false, $name = false)
  {
    $sizes = [
      'sm' => '112',
      'lg' => '1000',
    ];

    if(!array_key_exists($size, $sizes)) {
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    $contentPath = WRITEPATH . "content/product-photos/$size/";
    $contentPathname = $contentPath . $name;
    
    if(!file_exists($contentPath)) {
      mkdir($contentPath, 0777, true);
    }

    if(!file_exists($contentPathname)) {
      try {
        $from = WRITEPATH . "uploads/product-photos/$name";
        \Config\Services::image()
          ->withFile($from)
          ->resize($sizes[$size], 0,  true, 'width')
          ->save($contentPathname); // TODO: save as webp?
      } catch (ImageException $e) {
          echo $e->getMessage();
      }
    }

    header('Content-Type: ' . mime_content_type($contentPathname));
    header('Content-Length : ' . filesize($contentPathname));
    header('Content-Disposition: inline; filename="' . $name . '";');
    readfile($contentPathname);
    exit;
  }
}