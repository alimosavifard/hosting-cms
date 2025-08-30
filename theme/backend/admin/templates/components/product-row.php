<tr>
    <td><?php echo htmlspecialchars($product['name']); ?></td>
    <td><?php echo number_format($product['price']); ?> تومان</td>
    <td><?php echo htmlspecialchars($product['description']); ?></td>
    <td>
        <a href="<?php echo route('admin.editProduct', ['id' => $product['id']]); ?>">ویرایش</a>
        <a href="<?php echo route('admin.deleteProduct', ['id' => $product['id']]); ?>">حذف</a>
    </td>
</tr>
<?php