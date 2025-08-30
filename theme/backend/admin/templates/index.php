<?php
$pageTitle = 'پنل ادمین';
$csrfToken = $session->generateCsrfToken();
ob_start();
?>
<h2>مدیریت محصولات</h2>
<table>
    <tr>
        <th>نام</th>
        <th>قیمت</th>
        <th>توضیحات</th>
        <th>عملیات</th>
    </tr>
    <?php foreach ($products as $product): ?>
        <?php echo $theme->loadComponent('product-row', ['product' => $product]); ?>
    <?php endforeach; ?>
</table>
<?php
$content = ob_get_clean();
?>