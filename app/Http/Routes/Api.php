<?php

namespace FluentBoardsModes\App\Http\Routes;

defined('ABSPATH') or die;

class Api
{
    public static function register()
    {
        $namespace = 'fluent-boards-modes/v1';
        
        // Get boards with labels and assigned tasks (legacy endpoint for frontend compatibility)
        register_rest_route($namespace, '/boards', [
            'methods' => 'GET',
            'callback' => function($request) {
                if (!class_exists('\FluentBoardsModes\App\Http\Controllers\ApiController')) {
                    return new \WP_Error(
                        'class_not_found',
                        'ApiController class not found',
                        ['status' => 500]
                    );
                }
                $controller = new \FluentBoardsModes\App\Http\Controllers\ApiController();
                if (!method_exists($controller, 'getFocusModeData')) {
                    return new \WP_Error(
                        'method_not_found',
                        'getFocusModeData method not found',
                        ['status' => 500]
                    );
                }
                return $controller->getFocusModeData($request);
            },
            'permission_callback' => [__CLASS__, 'permissionCheck'],
        ]);
        
        // Get boards with labels and assigned tasks (new endpoint)
        register_rest_route($namespace, '/focus-mode/data', [
            'methods' => 'GET',
            'callback' => function($request) {
                if (!class_exists('\FluentBoardsModes\App\Http\Controllers\ApiController')) {
                    return new \WP_Error(
                        'class_not_found',
                        'ApiController class not found',
                        ['status' => 500]
                    );
                }
                $controller = new \FluentBoardsModes\App\Http\Controllers\ApiController();
                if (!method_exists($controller, 'getFocusModeData')) {
                    return new \WP_Error(
                        'method_not_found',
                        'getFocusModeData method not found',
                        ['status' => 500]
                    );
                }
                return $controller->getFocusModeData($request);
            },
            'permission_callback' => [__CLASS__, 'permissionCheck'],
        ]);
        
        // Get notes for a board
        register_rest_route($namespace, '/notes', [
            'methods' => 'GET',
            'callback' => function($request) {
                $controller = new \FluentBoardsModes\App\Http\Controllers\ApiController();
                return $controller->getNotes($request);
            },
            'permission_callback' => [__CLASS__, 'permissionCheck'],
        ]);
        
        // Create a note
        register_rest_route($namespace, '/notes', [
            'methods' => 'POST',
            'callback' => function($request) {
                $controller = new \FluentBoardsModes\App\Http\Controllers\ApiController();
                return $controller->createNote($request);
            },
            'permission_callback' => [__CLASS__, 'permissionCheck'],
        ]);
        
        // Update a note
        register_rest_route($namespace, '/notes/(?P<id>\d+)', [
            'methods' => 'PATCH',
            'callback' => function($request) {
                $controller = new \FluentBoardsModes\App\Http\Controllers\ApiController();
                return $controller->updateNote($request);
            },
            'permission_callback' => [__CLASS__, 'permissionCheck'],
            'args' => [
                'id' => [
                    'required' => true,
                    'type' => 'integer',
                    'validate_callback' => function($param) {
                        return is_numeric($param);
                    }
                ]
            ]
        ]);
        
        // Delete a note
        register_rest_route($namespace, '/notes/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => function($request) {
                $controller = new \FluentBoardsModes\App\Http\Controllers\ApiController();
                return $controller->deleteNote($request);
            },
            'permission_callback' => [__CLASS__, 'permissionCheck'],
            'args' => [
                'id' => [
                    'required' => true,
                    'type' => 'integer',
                    'validate_callback' => function($param) {
                        return is_numeric($param);
                    }
                ]
            ]
        ]);
    }

    /**
     * Permission callback for REST API routes
     * 
     * @return bool
     */
    public static function permissionCheck()
    {
        return current_user_can('read');
    }
}

