<?php

defined('ABSPATH') or die;

// Load Vite class
require_once FLUENT_BOARDS_MODES_PATH . 'app/Vite/Vite.php';

// Load REST API routes
require_once FLUENT_BOARDS_MODES_PATH . 'app/Http/Routes/Api.php';
require_once FLUENT_BOARDS_MODES_PATH . 'app/Http/Controllers/ApiController.php';

use FluentBoardsModes\App\Vite\Vite;
use FluentBoardsModes\App\Http\Routes\Api;

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
        
        // Register REST API routes
        add_action('rest_api_init', [Api::class, 'register']);
        
        // Add Notes menu item to board menu
        add_filter('fluent_boards/board_menu_items', [$this, 'addNotesMenuItem'], 10, 2);
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
        // add_submenu_page(
        //     'fluent-boards',
        //     __('Focus Modes', 'fluent-boards-modes'),
        //     __('Focus Modes', 'fluent-boards-modes'),
        //     $capability,
        //     'fluent-boards#/modes/focus-modes',
        //     [$this, 'renderModesPage']
        // );
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
                ],
                [
                    'key'       => 'para',
                    'label'     => __('Para', 'fluent-boards-modes'),
                    'permalink' => $baseUrl . 'modes/para'
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
        
        // Enqueue notes drawer mount script (built by Vite)
        Vite::enqueueScript(
            'fluent-boards-modes-notes-mount',
            'admin/js/notes-drawer-mount.js',
            ['fluent-boards_admin_app', 'fluent-boards-modes-app'],
            FLUENT_BOARDS_MODES_VERSION,
            true
        );
        
        // Explicitly enqueue NotesDrawerMount CSS if it exists
        $notesDrawerCssPath = FLUENT_BOARDS_MODES_PATH . 'assets/NotesDrawerMount.css';
        if (file_exists($notesDrawerCssPath)) {
            wp_enqueue_style(
                'fluent-boards-modes-notes-drawer',
                FLUENT_BOARDS_MODES_URL . 'assets/NotesDrawerMount.css',
                ['fluent-boards_admin_app'],
                FLUENT_BOARDS_MODES_VERSION
            );
        }

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
    
    /**
     * Add Notes menu item to board menu
     */
    public function addNotesMenuItem($menuItems, $board_id = null)
    {
        // Generate unique container ID
        $containerId = 'fbm-notes-modal-' . ($board_id ?: 'default');
        
        // Get the board ID from the filter parameter or try to get it from the request
        if (!$board_id) {
            // Try to get board ID from URL or request
            if (isset($_REQUEST['board_id'])) {
                $board_id = intval($_REQUEST['board_id']);
            } elseif (isset($_REQUEST['project_id'])) {
                $board_id = intval($_REQUEST['project_id']);
            }
        }
        
        // Create HTML with container - Vue component will be mounted by the mount script
        // Use {{BOARD_ID}} placeholder that fluent-boards will replace, or fallback to data attribute
        $html = sprintf(
            '<div id="%s" data-board-id="{{BOARD_ID}}" class="fbm-notes-container"></div>
            <script type="text/javascript">
            (function() {
                var containerId = "%s";
                
                // Function to get board ID from various sources
                function getBoardId() {
                    // Try to get from container data attribute first
                    var container = document.getElementById(containerId);
                    if (container) {
                        var boardId = container.getAttribute("data-board-id");
                        if (boardId && boardId !== "{{BOARD_ID}}" && boardId !== "") {
                            return boardId;
                        }
                    }
                    
                    // Try to get from fluent-boards global vars
                    if (typeof window.fluentAddonVars !== "undefined" && window.fluentAddonVars.board_id) {
                        return window.fluentAddonVars.board_id;
                    }
                    
                    // Try to get from URL hash (e.g., #/boards/123)
                    var hashMatch = window.location.hash.match(/\/boards?\/(\d+)/);
                    if (hashMatch && hashMatch[1]) {
                        return hashMatch[1];
                    }
                    
                    // Try to get from URL path
                    var pathMatch = window.location.pathname.match(/\/board\/(\d+)/);
                    if (pathMatch && pathMatch[1]) {
                        return pathMatch[1];
                    }
                    
                    return null;
                }
                
                // Function to mount the component
                function mountNotesComponent() {
                    if (typeof window.mountNotesDrawer === "function") {
                        var boardId = getBoardId();
                        if (boardId) {
                            console.log("Mounting NotesDrawer for board:", boardId);
                            window.mountNotesDrawer(containerId, boardId);
                        } else {
                            console.warn("Board ID not found, retrying...");
                            setTimeout(mountNotesComponent, 200);
                        }
                    } else {
                        console.warn("mountNotesDrawer function not available, retrying...");
                        // Retry after a short delay if mount function is not available yet
                        setTimeout(mountNotesComponent, 200);
                    }
                }
                
                // Also try to mount when modal opens (in case it\'s opened before script loads)
                setTimeout(function() {
                    var container = document.getElementById(containerId);
                    if (container && !container.__vue_app__ && !container.querySelector(".fbm-notes-drawer")) {
                        mountNotesComponent();
                    }
                }, 500);
                
                // Wait for DOM to be ready
                if (document.readyState === "loading") {
                    document.addEventListener("DOMContentLoaded", function() {
                        setTimeout(mountNotesComponent, 100);
                    });
                } else {
                    setTimeout(mountNotesComponent, 100);
                }
            })();
            </script>',
            esc_attr($containerId),
            esc_js($containerId)
        );
        
        // Add Notes menu item
        $menuItems['notes'] = [
            'key'       => 'notes',
            'label'     => __('Notes', 'fluent-boards-modes'),
            'type'      => 'custom',
            'position'  => 5, // Position after board labels
            'icon'      => '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/><path d="M5 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg>',
            'html'      => $html,
            'width'     => '800px',
            'render_in' => 'modal' // Render in modal instead of drawer
        ];
        
        return $menuItems;
    }
}

// Initialize the plugin
new FluentBoardsModes();
