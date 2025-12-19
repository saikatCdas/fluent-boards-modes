<?php

namespace FluentBoardsModes\App;

use Exception;

class Vite
{
    private array $moduleScripts = [];
    private bool $isScriptFilterAdded = false;
    private string $viteHostProtocol = 'http://';
    private string $viteHost = 'localhost';
    private string $vitePort = '8883';
    private string $resourceDirectory = 'resources/';

    protected static ?Vite $instance = null;
    public ?string $lastJsHandle = null;

    private ?string $manifestPath = "assets/manifest.json";
    private ?array $manifestData = null;

    public function __construct()
    {
        $serverConfigPath = FLUENT_BOARDS_MODES_PATH . 'config' . DIRECTORY_SEPARATOR . 'vite.json';
        if (file_exists($serverConfigPath)) {
            $serverConfig = json_decode(file_get_contents($serverConfigPath));
            $this->viteHost = $serverConfig->host ?: $this->viteHost;
            $this->viteHostProtocol = $serverConfig->protocol ?: $this->viteHostProtocol;
            $this->vitePort = $serverConfig->port ?: $this->vitePort;
            $this->manifestPath = $serverConfig->manifest_path ?: $this->manifestPath;
            $this->manifestPath = realpath(__DIR__) . '/../' . $this->manifestPath;
        }
    }

    private static function getInstance(): Vite
    {
        if (static::$instance === null) {
            static::$instance = new static();
            if (!static::$instance->usingDevMode()) {
                (static::$instance)->loadViteManifest();
            }
        }

        return static::$instance;
    }

    /**
     * @throws Exception
     */
    private function loadViteManifest()
    {
        if (!empty($this->manifestData)) {
            return;
        }

        if (!file_exists($this->manifestPath)) {
            throw new Exception('Vite Manifest Not Found. Run : npm run dev or npm run build');
        }

        if (!function_exists('get_filesystem_method')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        $manifestData = '';
        if (!(false === ($credentials = request_filesystem_credentials(site_url())) || !WP_Filesystem($credentials))) {
            global $wp_filesystem;
            $manifestData = $wp_filesystem->get_contents($this->manifestPath);
        }

        $this->manifestData = json_decode($manifestData, true);
    }

    public static function enqueueScript($handle, $src, $dependency = [], $version = null, $inFooter = false): Vite
    {
        return static::getInstance()->enqueue_script(
            $handle,
            $src,
            $dependency,
            $version,
            $inFooter
        );
    }

    private function enqueue_script($handle, $src, $dependency = [], $version = null, $inFooter = false): Vite
    {
        if (in_array($handle, $this->moduleScripts)) {
            if ($this->usingDevMode()) {
                $callerReference = (debug_backtrace(2)[1]);
                $fileName = explode('plugins', $callerReference['file']);
                $line = $callerReference['line'];
            }
        }

        $this->moduleScripts[] = $handle;
        $this->lastJsHandle = $handle;

        if (!$this->isScriptFilterAdded) {
            add_filter('script_loader_tag', function ($tag, $handle, $src) {
                return $this->addModuleToScript($tag, $handle, $src);
            }, 10, 3);
            $this->isScriptFilterAdded = true;
        }

        if (!$this->usingDevMode()) {
            $assetFile = $this->getFileFromManifest($src);
            $srcPath = $this->getProductionFilePath($assetFile);
        } else {
            $srcPath = $this->getVitePath() . $src;
        }

        if (empty($srcPath)) {
            return $this;
        }

        $version = empty($version) ? FLUENT_BOARDS_MODES_VERSION : $version;

        wp_enqueue_script(
            $handle,
            $srcPath,
            $dependency,
            $version,
            $inFooter
        );
        return $this;
    }

    private function getFileFromManifest($src)
    {
        if (isset($this->manifestData[$this->resourceDirectory . $src])) {
            return $this->manifestData[$this->resourceDirectory . $src];
        }

        if ($this->usingDevMode()) {
            throw new Exception(esc_html($src) . " file not found in vite manifest, Make sure it is in rollupOptions input and build again");
        }

        return '';
    }

    private function getProductionFilePath($file): string
    {
        if (!isset($file['file'])) {
            return '';
        }
        $assetPath = static::getAssetPath();

        $this->ensureChunkCssIsLoaded($file);

        return ($assetPath . $file['file']);
    }

    private function ensureChunkCssIsLoaded($file)
    {
        $assetPath = static::getAssetPath();
        if (isset($file['css']) && is_array($file['css'])) {
            foreach ($file['css'] as $key => $path) {
                $handle = $file['file'] . '_' . $key . '_css';
                $styleSrc = $assetPath . $path;
                
                wp_enqueue_style(
                    $handle,
                    $styleSrc,
                    [],
                    FLUENT_BOARDS_MODES_VERSION
                );
            }
        }
    }

    public function with($params)
    {
        if (!is_array($params) || !$this->isAssoc($params) || empty($this->lastJsHandle)) {
            $this->lastJsHandle = null;
            return;
        }

        foreach ($params as $key => $val) {
            wp_localize_script($this->lastJsHandle, $key, $val);
        }
        $this->lastJsHandle = null;
    }

    private function isAssoc($arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public static function enqueueStyle($handle, $src, $dependency = [], $version = null, $media = 'all')
    {
        static::getInstance()->enqueue_style(
            $handle,
            $src,
            $dependency,
            $version,
            $media
        );
    }

    private function enqueue_style($handle, $src, $dependency = [], $version = null, $media = 'all')
    {
        if (!$this->usingDevMode()) {
            $assetFile = (static::$instance)->getFileFromManifest($src);
            $srcPath = $this->getProductionFilePath($assetFile);
        } else {
            $srcPath = $this->getVitePath() . $src;
        }

        if (empty($srcPath)) {
            return;
        }

        $version = empty($version) ? FLUENT_BOARDS_MODES_VERSION : $version;

        wp_enqueue_style(
            $handle,
            $srcPath,
            $dependency,
            $version,
            $media
        );
    }

    public static function underDevelopment(): bool
    {
        return static::getInstance()->usingDevMode();
    }

    public function usingDevMode(): bool
    {
        // Check if Vite dev server is running by trying to connect to it
        $viteUrl = $this->getVitePath() . 'admin/bootstrap/app.js';
        $response = wp_remote_get($viteUrl, ['timeout' => 1]);
        return !is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200;
    }

    public function getVitePath(): string
    {
        $protocol = rtrim($this->viteHostProtocol, ':/');
        $host = rtrim($this->viteHost, '/');
        $port = $this->vitePort;
        $resource = ltrim($this->resourceDirectory, '/');

        return sprintf('%s://%s:%s/%s', $protocol, $host, $port, $resource);
    }

    public static function getEnqueuePath($path = ''): string
    {
        $vite = static::getInstance();

        if (!$vite->usingDevMode()) {
            $assetFile = $vite->getFileFromManifest($path);
            $srcPath = $vite->getProductionFilePath($assetFile);
        } else {
            $srcPath = $vite->getVitePath() . $path;
        }

        return $srcPath;
    }

    public static function getAssetUrl($path = ''): string
    {
        return esc_url(static::getInstance()->get_asset_url($path));
    }

    private function get_asset_url($path = ''): string
    {
        if (!$this->usingDevMode()) {
            return FLUENT_BOARDS_MODES_URL . 'assets' . DIRECTORY_SEPARATOR . $path;
        } else {
            return $this->getVitePath() . $path;
        }
    }

    static function getAssetPath(): string
    {
        return FLUENT_BOARDS_MODES_URL . 'assets/';
    }

    private function addModuleToScript($tag, $handle, $src)
    {
        if (in_array($handle, (static::$instance)->moduleScripts)) {
            return wp_get_script_tag(
                [
                    'src'  => esc_url($src),
                    'type' => 'module',
                    'id'   => $handle . '-js'
                ]
            );
        }
        return $tag;
    }
}
