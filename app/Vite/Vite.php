<?php

namespace FluentBoardsModes\App\Vite;

class Vite
{
    protected static $moduleScripts = [];
    protected static $resourceURL = 'http://localhost:5173/';
    protected static $assetsURL = FLUENT_BOARDS_MODES_URL . 'assets/';
    protected static $lastJsHandle = null;

    private static function isDev()
    {
        // If manifest exists, use production mode
        if (file_exists(FLUENT_BOARDS_MODES_PATH . 'assets/manifest.json')) {
            return false;
        }
        
        // In development mode when manifest doesn't exist, assume dev server is running
        return defined('WP_DEBUG') && WP_DEBUG;
    }

    public static function enqueueScript($handle, $src, $dependency = [], $version = false, $inFooter = false)
    {
        static::$moduleScripts[] = $handle;
        static::$lastJsHandle = $handle;
        $src = static::generateSrc($src);
        
        wp_enqueue_script($handle, $src, $dependency, $version, $inFooter);
        static::addModuleToScript();
    }

    public static function enqueueStyle($handle, $src, $dependency = [], $version = false, $media = 'all')
    {
        $src = static::generateSrc($src);
        wp_enqueue_style($handle, $src, $dependency, $version, $media);
    }

    private static function parseManifest()
    {
        static $manifest;
        if ($manifest) {
            return $manifest;
        }

        $manifestPath = FLUENT_BOARDS_MODES_PATH . 'assets/manifest.json';
        
        if (!file_exists($manifestPath)) {
            return null;
        }

        $manifestContent = file_get_contents($manifestPath);
        $manifest = json_decode($manifestContent, true);

        return $manifest;
    }

    private static function addModuleToScript()
    {
        add_filter('script_loader_tag', function ($tag, $handle, $src) {
            if (in_array($handle, static::$moduleScripts)) {
                $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
            }
            return $tag;
        }, 10, 3);
    }

    private static function generateSrc($src)
    {
        if (static::isDev()) {
            // In dev mode, prepend 'resources/' to match Vite input path
            return static::$resourceURL . 'resources/' . $src;
        }

        $manifest = static::parseManifest();
        
        if (!$manifest) {
            return static::$assetsURL . $src;
        }

        $manifestKey = 'resources/' . $src;
        
        if (isset($manifest[$manifestKey])) {
            $file = $manifest[$manifestKey];
            
            // Enqueue CSS files if they exist
            if (isset($file['css']) && is_array($file['css'])) {
                foreach ($file['css'] as $cssFile) {
                    wp_enqueue_style(
                        'fluent-boards-modes-' . md5($cssFile),
                        static::$assetsURL . $cssFile,
                        [],
                        FLUENT_BOARDS_MODES_VERSION
                    );
                }
            }
            
            return static::$assetsURL . $file['file'];
        }

        return static::$assetsURL . $src;
    }

    public static function with($params)
    {
        if (!is_array($params) || empty(static::$lastJsHandle)) {
            static::$lastJsHandle = null;
            return;
        }

        foreach ($params as $key => $val) {
            wp_localize_script(static::$lastJsHandle, $key, $val);
        }
        
        static::$lastJsHandle = null;
    }
}

