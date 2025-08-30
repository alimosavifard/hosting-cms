<?php
use App\Core\Session;
$session = new Session();
$csrfToken = $session->generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>افزودن محصول</title>
    <link rel="stylesheet" href="<?php echo $theme->getAssetPath('css/admin.css'); ?>">
</head>
<body>
    <header>
        <h1>افزودن محصول جدید</h1>
        <nav>
            <a href="<?php echo route('admin.index'); ?>">پنل ادمین</a>
            <a href="<?php echo route('user.logout'); ?>">خروج</a>
        </nav>
    </header>
    <main>
        <form action="<?php echo route('admin.addProduct'); ?>" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            <label>نام محصول:</label>
            <input type="text" name="name" required>
            <label>قیمت:</label>
            <input type="number" name="price" required>
            <label>توضیحات:</label>
            <textarea name="description" required></textarea>
            <button type="submit">افزودن</button>
        </form>
    </main>
</body>
</html>