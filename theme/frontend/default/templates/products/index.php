<?php
use App\Core\Session;

$session = new Session();
$csrfToken = $session->generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فروش هاست، دامنه و VPS</title>
    <link rel="stylesheet" href="<?php echo $theme->getAssetPath('css/style.css'); ?>">
</head>
<body>
    <header>
        <h1>فروش هاست، دامنه و VPS</h1>
        <nav>
            <a href="<?= route('home') ?>">خانه</a>
            <a href="<?= route('cart.index') ?>">سبد خرید</a>
            <?php if ($session->get('user_id')): ?>
                <a href="<?= route('user.logout') ?>">خروج</a>
            <?php else: ?>
                <a href="<?= route('user.login') ?>">ورود</a>
                <a href="<?= route('user.register') ?>">ثبت‌نام</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <h2>محصولات ما</h2>
        <div class="products">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p>قیمت: <?php echo number_format($product['price']); ?> تومان</p>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <form action="<?= route('cart.add') ?>" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                        <button type="submit">افزودن به سبد خرید</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer>
        <p>تمامی حقوق محفوظ است © 2025</p>
    </footer>
</body>
</html>
?>