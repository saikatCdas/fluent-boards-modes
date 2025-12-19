<?php

defined('ABSPATH') or die;

use FluentBoardsModes\App\Vite;

class FluentBoardsModes
{
    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        // Enqueue assets when fluent-boards app is loaded
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function enqueueAssets($hook)
    {
        // Only load on fluent-boards pages
        // Check if we're on a fluent-boards admin page
        if (strpos($hook, 'fluent_frame') === false && strpos($hook, 'fluent-boards') === false) {
            return;
        }

        // Wait for fluent-boards app to be ready
        add_action('admin_footer', [$this, 'enqueueAppAssets'], 999);
    }

    public function enqueueAppAssets()
    {
        // Enqueue Vue app
        Vite::enqueueScript(
            'fluent-boards-modes-app',
            'admin/bootstrap/app.js',
            [],
            FLUENT_BOARDS_MODES_VERSION,
            true
        );

        // Pass data to Vue app
        Vite::with([
            'fluentBoardsModes' => [
                'apiUrl' => rest_url('fluent-boards-modes/v1/'),
                'nonce' => wp_create_nonce('wp_rest'),
                'version' => FLUENT_BOARDS_MODES_VERSION,
            ]
        ]);
    }
}

// Initialize the plugin
new FluentBoardsModes();
