<?php
$pageTitle = 'محصولات';
$csrfToken = $session->generateCsrfToken();
?>
<h2>لیست محصولات</h2>
<div class="products">
    <?php foreach ($products as $product): ?>
        <?php echo $theme->loadComponent('product-card', [
            'product' => $product,
            'csrfToken' => $csrfToken
        ]); ?>
    <?php endforeach; ?>
</div>
