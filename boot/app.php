<?php

defined('ABSPATH') or die;

// Load Vite class
require_once FLUENT_BOARDS_MODES_PATH . 'app/Vite/Vite.php';

use FluentBoardsModes\App\Vite\Vite;

class FluentBoardsModes
{
    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        // Approve fluent-boards-modes slug for asset loading
        add_filter('fluent_boards/asset_listed_slugs', [$this, 'approveAssetSlug']);
        
        // Add Modes menu item to Fluent Boards
        add_filter('fluent_boards/core_menu_items', [$this, 'addModesMenuItem']);
        
        // Add Modes submenu to WordPress admin sidebar
        add_action('fluent_boards/after_core_menu_items', [$this, 'addModesSubmenu'], 10, 2);
        
        // Hook into fluent-boards asset loading
        add_action('fluent_boards/after_enqueue_assets', [$this, 'enqueueAssets']);
        
        // Also hook into fluent-boards loading app event
        add_action('fluent-boards_loading_app', [$this, 'enqueueAssets']);
    }

    public function addModesSubmenu($permissions, $isAdmin)
    {
        $user = get_user_by('ID', get_current_user_id());
        
        if (current_user_can('manage_options')) {
            $capability = 'manage_options';
        } else {
            $roles = array_values((array)$user->roles);
            $capability = !empty($roles) ? $roles[0] : 'read';
        }

        // Add main Modes submenu
        add_submenu_page(
            'fluent-boards',
            __('Modes', 'fluent-boards-modes'),
            __('Modes', 'fluent-boards-modes'),
            $capability,
            'fluent-boards#/modes',
            [$this, 'renderModesPage']
        );

        // Add Focus Modes submenu
        add_submenu_page(
            'fluent-boards',
            __('Focus Modes', 'fluent-boards-modes'),
            __('Focus Modes', 'fluent-boards-modes'),
            $capability,
            'fluent-boards#/modes/focus-modes',
            [$this, 'renderModesPage']
        );
    }

    public function renderModesPage()
    {
        // Use the same render method as fluent-boards
        if (class_exists('\FluentBoards\App\Hooks\Handlers\AdminMenuHandler')) {
            $handler = new \FluentBoards\App\Hooks\Handlers\AdminMenuHandler();
            $handler->render();
        }
    }

    public function addModesMenuItem($menuItems)
    {
        $baseUrl = fluent_boards_page_url();
        
        if (is_admin()) {
            $adminUrl = admin_url('admin.php?page=fluent-boards#/');
            if ($adminUrl != $baseUrl) {
                $baseUrl = $adminUrl;
            }
        }
        
        $menuItems['modes'] = [
            'key'       => 'modes',
            'label'     => __('Modes', 'fluent-boards-modes'),
            'permalink' => $baseUrl . 'modes',
            'sub_items' => [
                [
                    'key'       => 'focus-modes',
                    'label'     => __('Focus Modes', 'fluent-boards-modes'),
                    'permalink' => $baseUrl . 'modes/focus-modes'
                ]
            ]
        ];
        
        return $menuItems;
    }

    public function approveAssetSlug($slugs)
    {
        $slugs[] = '\/fluent-boards-modes\/';
        return $slugs;
    }

    public function enqueueAssets()
    {
        // Check if we're on fluent-boards page
        if (!isset($_REQUEST['page']) || $_REQUEST['page'] !== 'fluent-boards') {
            return;
        }

        // Enqueue Vue app (CSS is imported in app.js)
        Vite::enqueueScript(
            'fluent-boards-modes-app',
            'admin/bootstrap/app.js',
            ['fluent-boards_admin_app'], // Load after fluent-boards styles
            FLUENT_BOARDS_MODES_VERSION,
            true
        );

        // Add inline CSS override with high specificity
        add_action('admin_head', [$this, 'addInlineStyles'], 999);

        // Pass data to Vue app
        Vite::with([
            'fluentBoardsModes' => [
                'apiUrl' => rest_url('fluent-boards-modes/v1/'),
                'nonce' => wp_create_nonce('wp_rest'),
                'version' => FLUENT_BOARDS_MODES_VERSION,
                'i18n' => [
                    'Text' => __('Text', 'fluent-boards-modes'),
                    'Heading 2' => __('Heading 2', 'fluent-boards-modes'),
                    'Heading 3' => __('Heading 3', 'fluent-boards-modes'),
                    'Heading 4' => __('Heading 4', 'fluent-boards-modes'),
                    'Quote' => __('Quote', 'fluent-boards-modes'),
                    'Code' => __('Code', 'fluent-boards-modes'),
                    'List' => __('List', 'fluent-boards-modes'),
                    'Bullet List' => __('Bullet List', 'fluent-boards-modes'),
                    'Ordered List' => __('Ordered List', 'fluent-boards-modes'),
                ],
            ]
        ]);
    }

    public function addInlineStyles()
    {
        ?>
        <style type="text/css">
            .fframe_main-menu-items {
                overflow: visible !important;
            }
            /* Hide link tooltip visual components completely */
            milkdown-link-preview,
            milkdown-link-edit,
            milkdown-link-tooltip {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                pointer-events: none !important;
            }
        </style>
        <?php
    }
}

// Initialize the plugin
new FluentBoardsModes();
