<?php use App\Core\Router; ?>
<div class="product-card" data-product-id="<?php echo htmlspecialchars($product['id']); ?>">
    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
    <p>قیمت: <?php echo number_format($product['price']); ?> تومان</p>
    <p><?php echo htmlspecialchars($product['description']); ?></p>
    <form action="<?php echo Router::getRoute('cart.add'); ?>" method="post" class="add-to-cart-form">
        <input type="hidden" name="csrf_token" value="">
        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
        <button type="submit">افزودن به سبد خرید</button>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('<?php echo Router::getRoute('generate-csrf-token'); ?>')
        .then(response => response.json())
        .then(data => {
            document.querySelectorAll('.add-to-cart-form input[name="csrf_token"]').forEach(input => {
                input.value = data.csrf_token;
            });
        });
});
</script>