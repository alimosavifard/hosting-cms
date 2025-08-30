<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pageTitle ?? 'پنل ادمین'); ?></title>
    <?php foreach ($theme->getThemeConfig()['assets']['css'] ?? [] as $css): ?>
        <link rel="stylesheet" href="<?php echo $theme->getAssetPath($css); ?>">
    <?php endforeach; ?>
</head>
<body>
<?php