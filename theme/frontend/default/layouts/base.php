<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Hosting CMS'); ?></title>
    <?php foreach ($theme->getThemeConfig()['assets']['css'] ?? [] as $css): ?>
        <link rel="stylesheet" href="<?php echo $theme->getAssetPath($css); ?>">
    <?php endforeach; ?>
</head>
<body>

	<header>
		<h1>خوش آمدید به Hosting CMS</h1>
		<nav>
			<a href="<?php echo route('home'); ?>">خانه</a>
			<a href="<?php echo route('cart.index'); ?>">سبد خرید</a>
			<?php if ($session->get('user_id')): ?>
				<a href="<?php echo route('user.profile'); ?>">پروفایل</a>
				<a href="<?php echo route('user.logout'); ?>">خروج</a>
				<?php if ($session->get('is_admin')): ?>
					<a href="<?php echo route('admin.index'); ?>">پنل ادمین</a>
				<?php endif; ?>
			<?php else: ?>
				<a href="<?php echo route('user.login'); ?>">ورود</a>
				<a href="<?php echo route('user.register'); ?>">ثبت‌نام</a>
			<?php endif; ?>
		</nav>
	</header>

	<main>
		<?php echo $content; ?>
	</main>
		
	<footer>
		<p>&copy; <?php echo date('Y'); ?> Hosting CMS. تمام حقوق محفوظ است.</p>
	</footer>
</body>
</html>
