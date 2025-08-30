<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\Theme;
use App\Models\Product;
use App\Core\CacheManager;

class ProductController
{
    protected $theme;
    protected $session;
    protected $cacheManager;

    public function __construct()
    {
        $this->theme = new Theme();
        $this->session = new Session();
        $this->cacheManager = new CacheManager();
    }

    public function index(Request $request)
    {
        return $this->cacheManager->getCachedResponse($request, function() use ($request) {
            $productModel = new Product();
            $products = $productModel->getAll();
            $csrfToken = $this->session->generateCsrfToken();

            return $this->theme->loadTemplate('index', [
                'products' => $products,
                'theme' => $this->theme,
                'pageTitle' => 'محصولات',
                'session' => $this->session,
                'csrfToken' => $csrfToken
            ], false); // غیرفعال کردن کش در Theme برای جلوگیری از تداخل
        });
    }

    public function addProduct(Request $request)
    {
        // منطق افزودن محصول
        $productModel = new Product();
        $productModel->create($request->getPost());
        
        // پاک کردن کش مرتبط
        $this->cacheManager->clearCache('product.index');
        $this->cacheManager->clearCache('home');
        
        return "محصول با موفقیت اضافه شد";
    }

    public function editProduct(Request $request)
    {
        // منطق ویرایش محصول
        $productModel = new Product();
        $productModel->update($request->getPost());
        
        // پاک کردن کش مرتبط
        $this->cacheManager->clearCache('product.index');
        $this->cacheManager->clearCache('home');
        
        return "محصول با موفقیت ویرایش شد";
    }

    public function deleteProduct(Request $request)
    {
        // منطق حذف محصول
        $productModel = new Product();
        $productModel->delete($request->getPost('product_id'));
        
        // پاک کردن کش مرتبط
        $this->cacheManager->clearCache('product.index');
        $this->cacheManager->clearCache('home');
        
        return "محصول با موفقیت حذف شد";
    }
}
?>