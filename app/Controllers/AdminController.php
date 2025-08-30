<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Theme;
use App\Models\Product;

class AdminController
{
    private $theme;
    private $session;

    public function __construct()
    {
        $this->theme = new Theme(true);
        $this->session = new Session();
        if (!$this->session->get('is_admin')) {
            Response::redirectNamed('user.login');
        }
    }

    public function index(Request $request)
    {
        $productModel = new Product();
        $products = $productModel->getAll();
        return $this->theme->loadTemplate('index', [
            'products' => $products,
            'theme' => $this->theme,
            'pageTitle' => 'پنل ادمین',
            'session' => $this->session
        ]);
    }

    public function addProduct(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->getPost();
            $productModel = new Product();
            $productModel->create([
                'name' => $data['name'],
                'price' => $data['price'],
                'description' => $data['description']
            ]);
            Response::redirectNamed('admin.index');
        }
        return $this->theme->loadTemplate('add_product', [
            'theme' => $this->theme,
            'pageTitle' => 'افزودن محصول',
            'session' => $this->session
        ]);
    }

    public function editProduct(Request $request)
    {
        $id = $request->getQuery('id');
        $productModel = new Product();
        $product = $productModel->find($id);
        if (!$product) {
            Response::redirectNamed('admin.index');
        }
        if ($request->isPost()) {
            $data = $request->getPost();
            $productModel->update($id, [
                'name' => $data['name'],
                'price' => $data['price'],
                'description' => $data['description']
            ]);
            Response::redirectNamed('admin.index');
        }
        return $this->theme->loadTemplate('edit_product', [
            'product' => $product,
            'theme' => $this->theme,
            'pageTitle' => 'ویرایش محصول',
            'session' => $this->session
        ]);
    }

    public function deleteProduct(Request $request)
    {
        $id = $request->getQuery('id');
        $productModel = new Product();
        $productModel->delete($id);
        Response::redirectNamed('admin.index');
    }
}
?>