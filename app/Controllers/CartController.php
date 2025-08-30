<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Theme;
use App\Models\Product;
use App\Models\Order;

class CartController
{
    private $theme;
    private $session;

    public function __construct()
    {
        $this->theme = new Theme();
        $this->session = new Session();
    }

    public function index(Request $request)
    {
        $cart = $this->session->get('cart', []);
        $productModel = new Product();
        $products = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $productModel->find($productId);
            if ($product) {
                $products[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product['price'] * $quantity
                ];
                $total += $product['price'] * $quantity;
            }
        }

        return $this->theme->loadTemplate('cart/index', [
            'products' => $products,
            'total' => $total
        ]);
    }

    public function add(Request $request)
    {
        if ($request->isPost() && $this->session->verifyCsrfToken($request->getPost('csrf_token'))) {
            $productId = $request->getPost('product_id');
            $cart = $this->session->get('cart', []);
            $cart[$productId] = ($cart[$productId] ?? 0) + 1;
            $this->session->set('cart', $cart);
        }
        Response::redirectNamed('cart.index');
    }

    public function checkout(Request $request)
    {
        if (!$this->session->get('user_id')) {
            Response::redirectNamed('user.login');
        }

        if ($request->isPost() && $this->session->verifyCsrfToken($request->getPost('csrf_token'))) {
            $cart = $this->session->get('cart', []);
            $productModel = new Product();
            $orderModel = new Order();

            foreach ($cart as $productId => $quantity) {
                $product = $productModel->find($productId);
                if ($product) {
                    $orderModel->create([
                        'user_id' => $this->session->get('user_id'),
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'total_price' => $product['price'] * $quantity
                    ]);
                }
            }

            $this->session->set('cart', []);
            Response::redirectNamed('cart.success');
        }

        return $this->theme->loadTemplate('cart/checkout');
    }

    public function success(Request $request)
    {
        return $this->theme->loadTemplate('cart/success');
    }
}
?>