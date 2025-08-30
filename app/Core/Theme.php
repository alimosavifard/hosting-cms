<?php
namespace App\Core;

use App\Core\Config;
use App\Core\Cache;

class Theme
{
    private $config;
    private $logger;
    private $cache;
    private $activeTheme;
    private $themePath;
    private $isAdmin;
    private $themeConfig;

    public function __construct(bool $isAdmin = false)
    {
        $this->config = Config::getInstance();
        $this->logger = Logger::getInstance('theme');
        $this->cache = new Cache();
        $this->isAdmin = $isAdmin;

        if ($this->isAdmin) {
            $this->activeTheme = 'admin';
            $this->themePath = __DIR__ . '/../../theme/backend/admin/';
        } else {
            $this->activeTheme = $this->config->get('theme', 'default');
            $this->themePath = __DIR__ . '/../../theme/frontend/' . $this->activeTheme . '/';
            if (!is_dir($this->themePath)) {
                $this->logger->error("Theme {$this->activeTheme} not found, falling back to default");
                $this->activeTheme = 'default';
                $this->themePath = __DIR__ . '/../../theme/frontend/default/';
            }
        }

        $configPath = $this->themePath . 'config.json';
        if (file_exists($configPath)) {
            $this->themeConfig = json_decode(file_get_contents($configPath), true);
            $this->logger->info("Loaded theme config: {$configPath}");
        } else {
            $this->themeConfig = ['layout' => 'base.php'];
            $this->logger->warning("No config.json found for theme {$this->activeTheme}, using default layout");
        }
    }

    public function loadTemplate($template, $data = [], $useCache = true)
    {
        $cacheKey = $this->cache->generateCacheKey($template, $data);

        // بررسی کش
        if ($useCache) {
            $cachedOutput = $this->cache->get($cacheKey);
            if ($cachedOutput !== false) {
                return $cachedOutput;
            }
        }

        $templatePath = $this->themePath . 'templates/' . $template . '.php';
        
        if (!file_exists($templatePath)) {
            $this->logger->error("Template $template not found in path {$this->themePath}");
            throw new \Exception("قالب $template یافت نشد");
        }

        extract($data, EXTR_SKIP);
        
        ob_start();
        require $templatePath;
        $content = ob_get_clean();
        
        $layoutPath = $this->themePath . 'layouts/' . ($this->themeConfig['layout'] ?? 'base.php');
        if (!file_exists($layoutPath)) {
            $this->logger->error("Layout {$this->themeConfig['layout']} not found in path {$this->themePath}layouts/");
            throw new \Exception("طرح‌بندی {$this->themeConfig['layout']} یافت نشد");
        }

        ob_start();
        require $layoutPath;
        $output = ob_get_clean();

        // ذخیره در کش
        if ($useCache) {
            $this->cache->set($cacheKey, $output);
        }

        return $output;
    }

    public function loadComponent($component, $data = [], $useCache = true)
    {
        $cacheKey = $this->cache->generateCacheKey('component_' . $component, $data);

        // بررسی کش
        if ($useCache) {
            $cachedOutput = $this->cache->get($cacheKey);
            if ($cachedOutput !== false) {
                return $cachedOutput;
            }
        }

        $componentPath = $this->themePath . 'templates/components/' . $component . '.php';
        if (!file_exists($componentPath)) {
            $this->logger->error("Component $component not found in path {$this->themePath}templates/components/");
            return '';
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $componentPath;
        $output = ob_get_clean();

        // ذخیره در کش
        if ($useCache) {
            $this->cache->set($cacheKey, $output);
        }

        return $output;
    }

    public function clearCache($prefix = null)
    {
        $this->cache->clear($prefix);
    }

    public function getAssetPath($asset): string
    {
        $basePath = $this->isAdmin ? 'backend/admin' : 'frontend/' . $this->activeTheme;
        $minifiedAsset = str_replace(['.css', '.js'], ['.min.css', '.min.js'], $asset);
        return $this->config->get('app_url') . '/assets/' . $basePath . '/' . ltrim($minifiedAsset, '/');
    }

    public function getThemeConfig(): array
    {
        return $this->themeConfig;
    }

    public function copyAssets()
    {
        $sourcePath = $this->themePath . 'assets/';
        $destPath = __DIR__ . '/../../public/assets/' . ($this->isAdmin ? 'backend/admin' : 'frontend/' . $this->activeTheme) . '/';
        
        $this->logger->info("Attempting to copy assets from {$sourcePath} to {$destPath}");
        
        if (!is_dir($sourcePath)) {
            $this->logger->warning("Assets directory not found at {$sourcePath}");
            return;
        }

        if (!is_dir($destPath)) {
            $this->logger->info("Creating destination directory: {$destPath}");
            if (!mkdir($destPath, 0755, true)) {
                $this->logger->error("Failed to create destination directory: {$destPath}");
                return;
            }
        }

        $cssFiles = glob($sourcePath . 'css/*.css');
        $this->logger->info("Found " . count($cssFiles) . " CSS files in {$sourcePath}css/");
        
        foreach ($cssFiles as $file) {
            if (!is_readable($file)) {
                $this->logger->error("CSS file not readable: {$file}");
                continue;
            }
            $fileName = basename($file);
            $destFile = $destPath . 'css/' . str_replace('.css', '.min.css', $fileName);
            $this->logger->info("Processing CSS file: {$fileName}");
            $content = file_get_contents($file);
            if ($content === false) {
                $this->logger->error("Failed to read CSS file: {$file}");
                continue;
            }
            $this->minifyCss($content, $destFile);
            $this->logger->info("Copied and minified CSS: {$fileName} to {$destFile}");
        }

        $jsFiles = glob($sourcePath . 'js/*.js');
        $this->logger->info("Found " . count($jsFiles) . " JS files in {$sourcePath}js/");
        
        foreach ($jsFiles as $file) {
            if (!is_readable($file)) {
                $this->logger->error("JS file not readable: {$file}");
                continue;
            }
            $fileName = basename($file);
            $destFile = $destPath . 'js/' . str_replace('.js', '.min.js', $fileName);
            $this->logger->info("Processing JS file: {$fileName}");
            $content = file_get_contents($file);
            if ($content === false) {
                $this->logger->error("Failed to read JS file: {$file}");
                continue;
            }
            $this->minifyJs($content, $destFile);
            $this->logger->info("Copied and minified JS: {$fileName} to {$destFile}");
        }
    }

    private function minifyCss($css, $outputFile)
    {
        if (empty($css)) {
            $this->logger->error("CSS content is empty for {$outputFile}");
            return;
        }

        $this->logger->info("Minifying CSS for {$outputFile}");
        $css = preg_replace('#/\*[^*]*\*+([^/][^*]*\*+)*\/#', '', $css);
        $css = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $css ?? '');
        $css = str_replace('{ ', '{', $css);
        $css = str_replace(' }', '}', $css);
        $css = str_replace('; ', ';', $css);
        $css = str_replace(': ', ':', $css);

        $dir = dirname($outputFile);
        if (!is_dir($dir)) {
            $this->logger->info("Creating CSS directory: {$dir}");
            if (!mkdir($dir, 0755, true)) {
                $this->logger->error("Failed to create CSS directory: {$dir}");
                return;
            }
        }

        $result = file_put_contents($outputFile, $css);
        if ($result === false) {
            $this->logger->error("Failed to write CSS file: {$outputFile}");
        } else {
            $this->logger->info("Successfully wrote CSS file: {$outputFile}");
        }
    }

    private function minifyJs($js, $outputFile)
    {
        if (empty($js)) {
            $this->logger->error("JS content is empty for {$outputFile}");
            return;
        }

        $this->logger->info("Minifying JS for {$outputFile}");
        $js = preg_replace('#//.*?\n#', '', $js);
        $js = preg_replace('#/\*.*?\*/#s', '', $js);
        $js = preg_replace('#\s+#', ' ', $js);
        $js = str_replace([' {', '} ', '( ', ' )', '; '], ['{', '}', '(', ')', ';'], $js ?? '');

        $dir = dirname($outputFile);
        if (!is_dir($dir)) {
            $this->logger->info("Creating JS directory: {$dir}");
            if (!mkdir($dir, 0755, true)) {
                $this->logger->error("Failed to create JS directory: {$dir}");
                return;
            }
        }

        $result = file_put_contents($outputFile, $js);
        if ($result === false) {
            $this->logger->error("Failed to write JS file: {$outputFile}");
        } else {
            $this->logger->info("Successfully wrote JS file: {$outputFile}");
        }
    }
}
?>