<?php

namespace FluentBoardsModes\App\Http\Controllers;

defined('ABSPATH') or die;

class ApiController
{
    /**
     * Get focus mode data - boards with labels and assigned tasks
     * 
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function getFocusModeData($request)
    {
        // Check if Fluent Boards is active
        if (!class_exists('\FluentBoards\App\Models\Board')) {
            return new \WP_REST_Response([
                'boards' => [],
                'tasks' => [],
                'message' => 'Fluent Boards plugin is not active',
            ], 200);
        }

        $userId = get_current_user_id();
        if (!$userId) {
            return new \WP_REST_Response([
                'boards' => [],
                'tasks' => [],
                'message' => 'User not authenticated',
            ], 401);
        }

        try {
            $boardClass = '\FluentBoards\App\Models\Board';
            $taskClass = '\FluentBoards\App\Models\Task';
            $labelClass = '\FluentBoards\App\Models\Label';
            
            // Fetch all boards accessible by user
            $boardsQuery = $boardClass::whereNull('archived_at');

            // Use byAccessUser if available, otherwise filter by created_by
            if (method_exists($boardClass, 'byAccessUser')) {
                $boardsQuery = $boardsQuery->byAccessUser($userId);
            } else {
                $boardsQuery = $boardsQuery->where('created_by', $userId);
            }

            $boardsQuery = $boardsQuery->orderBy('created_at', 'DESC');

            // Apply search if provided
            $search = $request->get_param('search');
            if (!empty($search)) {
                $boardsQuery = $boardsQuery->where('title', 'like', '%' . sanitize_text_field($search) . '%');
            }

            $boards = $boardsQuery->get();

            // Format boards with labels
            $formattedBoards = [];
            $boardIds = [];
            
            if ($boards) {
                foreach ($boards as $board) {
                    $boardIds[] = $board->id;
                    
                    // Fetch labels for this board
                    $labels = [];
                    if (class_exists($labelClass)) {
                        try {
                            // Label model has a global scope that filters by type='label'
                            $boardLabels = $labelClass::where('board_id', $board->id)
                                ->orderBy('position', 'ASC')
                                ->orderBy('created_at', 'ASC')
                                ->get();
                            
                            foreach ($boardLabels as $label) {
                                $labels[] = [
                                    'id' => (int) $label->id,
                                    'title' => $label->title ?? '',
                                    'color' => $label->color ?? '',
                                    'bg_color' => $label->bg_color ?? '',
                                    'board_id' => (int) $label->board_id,
                                    'slug' => $label->slug ?? '',
                                ];
                            }
                        } catch (\Exception $e) {
                            // If label fetching fails, continue with empty labels array
                            $labels = [];
                        }
                    }
                    
                    // Calculate task statistics for this board
                    $totalTasks = $taskClass::where('board_id', $board->id)
                        ->whereNull('archived_at')
                        ->whereNull('parent_id')
                        ->count();
                    
                    $tasksDone = $taskClass::where('board_id', $board->id)
                        ->where('status', 'closed')
                        ->whereNull('archived_at')
                        ->whereNull('parent_id')
                        ->count();
                    
                    $tasksToDo = $totalTasks - $tasksDone;
                    
                    $formattedBoards[] = [
                        'id' => (int) $board->id,
                        'title' => $board->title ?? '',
                        'description' => $board->description ?? '',
                        'type' => $board->type ?? 'to-do',
                        'background' => $board->background ?? '',
                        'created_at' => $board->created_at ?? '',
                        'updated_at' => $board->updated_at ?? '',
                        'labels' => $labels,
                        'tasks_count' => $totalTasks,
                        'tasks_to_do' => $tasksToDo,
                        'tasks_done' => $tasksDone,
                    ];
                }
            }

            // Fetch all assigned tasks for the user using User model's assignedTasks relationship
            $assignedTasks = [];
            if (class_exists('\FluentBoards\App\Models\User') && !empty($boardIds)) {
                try {
                    $userModel = \FluentBoards\App\Models\User::find($userId);
                    
                    if ($userModel && method_exists($userModel, 'assignedTasks')) {
                        // Get tasks assigned to the user that are in the user's accessible boards
                        $tasks = $userModel->assignedTasks()
                            ->whereIn('board_id', $boardIds)
                            ->whereNull('archived_at')
                            ->whereNull('parent_id') // Only parent tasks, not subtasks
                            ->orderBy('due_at', 'ASC')
                            ->get();
                        
                        foreach ($tasks as $task) {
                            $assignedTasks[] = [
                                'id' => (int) $task->id,
                                'title' => $task->title ?? '',
                                'description' => $task->description ?? '',
                                'board_id' => (int) $task->board_id,
                                'status' => $task->status ?? 'open',
                                'priority' => $task->priority ?? '',
                                'stage_id' => $task->stage_id ?? null,
                                'due_at' => $task->due_at ?? null,
                                'started_at' => $task->started_at ?? null,
                                'created_at' => $task->created_at ?? '',
                                'updated_at' => $task->updated_at ?? '',
                            ];
                        }
                    } else {
                        // Fallback: Query tasks directly using fbs_relations table
                        global $wpdb;
                        $relationsTable = $wpdb->prefix . 'fbs_relations';
                        
                        // Check if constant exists for object type
                        $objectType = 'task_assignee'; // Default value
                        if (class_exists('\FluentBoards\App\Services\Constant')) {
                            $objectType = \FluentBoards\App\Services\Constant::OBJECT_TYPE_TASK_ASSIGNEE ?? 'task_assignee';
                        }
                        
                        $taskIds = $wpdb->get_col($wpdb->prepare(
                            "SELECT object_id FROM {$relationsTable} 
                            WHERE foreign_id = %d 
                            AND object_type = %s",
                            $userId,
                            $objectType
                        ));
                        
                        if (!empty($taskIds)) {
                            $tasks = $taskClass::whereIn('id', $taskIds)
                                ->whereIn('board_id', $boardIds)
                                ->whereNull('archived_at')
                                ->whereNull('parent_id')
                                ->orderBy('due_at', 'ASC')
                                ->get();
                            
                            foreach ($tasks as $task) {
                                $assignedTasks[] = [
                                    'id' => (int) $task->id,
                                    'title' => $task->title ?? '',
                                    'description' => $task->description ?? '',
                                    'board_id' => (int) $task->board_id,
                                    'status' => $task->status ?? 'open',
                                    'priority' => $task->priority ?? '',
                                    'stage_id' => $task->stage_id ?? null,
                                    'due_at' => $task->due_at ?? null,
                                    'started_at' => $task->started_at ?? null,
                                    'created_at' => $task->created_at ?? '',
                                    'updated_at' => $task->updated_at ?? '',
                                ];
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // If task fetching fails, continue with empty tasks array
                    $assignedTasks = [];
                }
            }

            return new \WP_REST_Response([
                'boards' => $formattedBoards,
                'tasks' => $assignedTasks,
                'message' => 'Focus mode data retrieved successfully',
            ], 200);
        } catch (\Exception $e) {
            return new \WP_REST_Response([
                'message' => 'Failed to retrieve focus mode data: ' . $e->getMessage(),
                'boards' => [],
                'tasks' => [],
            ], 500);
        }
    }
    
    /**
     * Get notes for a board
     * 
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function getNotes($request)
    {
        $boardId = $request->get_param('board_id');
        $userId = get_current_user_id();
        
        if (!$userId) {
            return new \WP_REST_Response([
                'data' => [],
                'message' => 'User not authenticated',
            ], 401);
        }
        
        try {
            // Check if Fluent Boards is active
            if (!class_exists('\FluentBoards\App\Models\Task')) {
                return new \WP_REST_Response([
                    'data' => [],
                    'message' => 'Fluent Boards plugin is not active',
                ], 500);
            }
            
            $taskClass = '\FluentBoards\App\Models\Task';
            
            // Get tasks with type='note'
            $notesQuery = $taskClass::withoutGlobalScope('type')
                ->where('type', 'note')
                ->whereNull('archived_at');
            
            // Filter by board_id if provided
            if (!empty($boardId)) {
                $boardId = intval($boardId);
                $notesQuery = $notesQuery->where('board_id', $boardId);
            } else {
                // If no board_id, get notes from all boards the user has access to
                $boardClass = '\FluentBoards\App\Models\Board';
                $userBoardsQuery = $boardClass::whereNull('archived_at');
                
                // Use byAccessUser if available, otherwise filter by created_by
                if (method_exists($boardClass, 'byAccessUser')) {
                    $userBoardsQuery = $userBoardsQuery->byAccessUser($userId);
                } else {
                    $userBoardsQuery = $userBoardsQuery->where('created_by', $userId);
                }
                
                $userBoardIds = $userBoardsQuery->pluck('id')->toArray();
                
                if (!empty($userBoardIds)) {
                    $notesQuery = $notesQuery->whereIn('board_id', $userBoardIds);
                } else {
                    // No accessible boards, return empty
                    return new \WP_REST_Response([
                        'data' => [],
                        'message' => 'Notes retrieved successfully',
                    ], 200);
                }
            }
            
            $notes = $notesQuery->orderBy('created_at', 'DESC')->get();
            
            // Get all board IDs from notes
            $boardIds = $notes->pluck('board_id')->unique()->toArray();
            
            // Fetch boards to get their names
            $boardClass = '\FluentBoards\App\Models\Board';
            $boards = [];
            if (!empty($boardIds)) {
                $boardsQuery = $boardClass::whereIn('id', $boardIds);
                $boardsData = $boardsQuery->get();
                foreach ($boardsData as $board) {
                    $boards[$board->id] = [
                        'id' => (int) $board->id,
                        'name' => $board->title ?? '',
                        'title' => $board->title ?? ''
                    ];
                }
            }
            
            // Format notes
            $formattedNotes = [];
            foreach ($notes as $note) {
                $boardInfo = $boards[$note->board_id] ?? null;
                $formattedNotes[] = [
                    'id' => (int) $note->id,
                    'board_id' => (int) $note->board_id,
                    'board_name' => $boardInfo['name'] ?? null,
                    'title' => $note->title ?? '',
                    'content' => $note->description ?? '',
                    'created_at' => $note->created_at ?? '',
                    'updated_at' => $note->updated_at ?? '',
                    'created_by' => (int) $note->created_by,
                ];
            }
            
            return new \WP_REST_Response([
                'data' => $formattedNotes,
                'message' => 'Notes retrieved successfully',
            ], 200);
        } catch (\Exception $e) {
            return new \WP_REST_Response([
                'data' => [],
                'message' => 'Failed to retrieve notes: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Create a note
     * 
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function createNote($request)
    {
        $boardId = $request->get_param('board_id');
        $title = $request->get_param('title');
        $content = $request->get_param('content');
        
        if (empty($boardId)) {
            return new \WP_REST_Response([
                'data' => null,
                'message' => 'Board ID is required',
            ], 400);
        }
        
        if (empty($content)) {
            return new \WP_REST_Response([
                'data' => null,
                'message' => 'Note content is required',
            ], 400);
        }
        
        $boardId = intval($boardId);
        $userId = get_current_user_id();
        
        if (!$userId) {
            return new \WP_REST_Response([
                'data' => null,
                'message' => 'User not authenticated',
            ], 401);
        }
        
        // Check if Fluent Boards is active
        if (!class_exists('\FluentBoards\App\Models\Task') || !class_exists('\FluentBoards\App\Models\Board')) {
            return new \WP_REST_Response([
                'data' => null,
                'message' => 'Fluent Boards plugin is not active',
            ], 500);
        }
        
        // Verify board exists
        $board = \FluentBoards\App\Models\Board::find($boardId);
        if (!$board) {
            return new \WP_REST_Response([
                'data' => null,
                'message' => 'Board not found',
            ], 404);
        }
        
        try {
            $taskClass = '\FluentBoards\App\Models\Task';
            
            // Get the first stage for the board (notes don't need a specific stage, but tasks require one)
            $stageClass = '\FluentBoards\App\Models\Stage';
            $stage = $stageClass::where('board_id', $boardId)
                ->orderBy('position', 'ASC')
                ->first();
            
            if (!$stage) {
                return new \WP_REST_Response([
                    'data' => null,
                    'message' => 'No stage found for this board',
                ], 400);
            }
            
            // Create task with type='note'
            // The Task model's boot method sets type based on board, so we need to update it after creation
            $task = $taskClass::withoutGlobalScope('type')->create([
                'title' => sanitize_text_field($title ?? ''),
                'board_id' => $boardId,
                'stage_id' => $stage->id,
                'description' => wp_kses_post($content),
                'status' => 'open',
                'created_by' => $userId,
                'position' => 0, // Notes don't need position
            ]);
            
            // Update type to 'note' after creation (boot method sets it to 'task' or 'roadmap')
            $task->withoutGlobalScope('type')->where('id', $task->id)->update(['type' => 'note']);
            $task->refresh();
            
            return new \WP_REST_Response([
                'data' => [
                    'id' => (int) $task->id,
                    'board_id' => (int) $task->board_id,
                    'title' => $task->title ?? '',
                    'content' => $task->description ?? '',
                    'created_at' => $task->created_at ?? '',
                    'updated_at' => $task->updated_at ?? '',
                    'created_by' => (int) $task->created_by,
                ],
                'message' => 'Note created successfully',
            ], 201);
        } catch (\Exception $e) {
            return new \WP_REST_Response([
                'data' => null,
                'message' => 'Failed to create note: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Update a note
     * 
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function updateNote($request)
    {
        $noteId = $request->get_param('id');
        $title = $request->get_param('title');
        $content = $request->get_param('content');
        
        if (empty($noteId)) {
            return new \WP_REST_Response([
                'data' => null,
                'message' => 'Note ID is required',
            ], 400);
        }
        
        if (empty($content)) {
            return new \WP_REST_Response([
                'data' => null,
                'message' => 'Note content is required',
            ], 400);
        }
        
        $noteId = intval($noteId);
        $userId = get_current_user_id();
        
        if (!$userId) {
            return new \WP_REST_Response([
                'data' => null,
                'message' => 'User not authenticated',
            ], 401);
        }
        
        // Check if Fluent Boards is active
        if (!class_exists('\FluentBoards\App\Models\Task')) {
            return new \WP_REST_Response([
                'data' => null,
                'message' => 'Fluent Boards plugin is not active',
            ], 500);
        }
        
        try {
            $taskClass = '\FluentBoards\App\Models\Task';
            
            // Get the note (task with type='note')
            $note = $taskClass::withoutGlobalScope('type')
                ->where('id', $noteId)
                ->where('type', 'note')
                ->first();
            
            if (!$note) {
                return new \WP_REST_Response([
                    'data' => null,
                    'message' => 'Note not found',
                ], 404);
            }
            
            // Check if user created the note or is admin
            if ((int) $note->created_by !== $userId && !current_user_can('manage_options')) {
                return new \WP_REST_Response([
                    'data' => null,
                    'message' => 'You do not have permission to update this note',
                ], 403);
            }
            
            // Update the note
            $note->withoutGlobalScope('type')->where('id', $noteId)->update([
                'title' => sanitize_text_field($title ?? ''),
                'description' => wp_kses_post($content),
            ]);
            
            $note->refresh();
            
            return new \WP_REST_Response([
                'data' => [
                    'id' => (int) $note->id,
                    'board_id' => (int) $note->board_id,
                    'title' => $note->title ?? '',
                    'content' => $note->description ?? '',
                    'created_at' => $note->created_at ?? '',
                    'updated_at' => $note->updated_at ?? '',
                    'created_by' => (int) $note->created_by,
                ],
                'message' => 'Note updated successfully',
            ], 200);
        } catch (\Exception $e) {
            return new \WP_REST_Response([
                'data' => null,
                'message' => 'Failed to update note: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Delete a note
     * 
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function deleteNote($request)
    {
        $noteId = $request->get_param('id');
        $userId = get_current_user_id();
        
        if (!$userId) {
            return new \WP_REST_Response([
                'message' => 'User not authenticated',
            ], 401);
        }
        
        if (empty($noteId)) {
            return new \WP_REST_Response([
                'message' => 'Note ID is required',
            ], 400);
        }
        
        $noteId = intval($noteId);
        
        try {
            // Check if Fluent Boards is active
            if (!class_exists('\FluentBoards\App\Models\Task')) {
                return new \WP_REST_Response([
                    'message' => 'Fluent Boards plugin is not active',
                ], 500);
            }
            
            $taskClass = '\FluentBoards\App\Models\Task';
            
            // Get the note (task with type='note')
            $note = $taskClass::withoutGlobalScope('type')
                ->where('id', $noteId)
                ->where('type', 'note')
                ->first();
            
            if (!$note) {
                return new \WP_REST_Response([
                    'message' => 'Note not found',
                ], 404);
            }
            
            // Check if user created the note or is admin
            if ((int) $note->created_by !== $userId && !current_user_can('manage_options')) {
                return new \WP_REST_Response([
                    'message' => 'You do not have permission to delete this note',
                ], 403);
            }
            
            // Delete the task (note)
            $note->delete();
            
            return new \WP_REST_Response([
                'message' => 'Note deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return new \WP_REST_Response([
                'message' => 'Failed to delete note: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Create notes table if it doesn't exist
     * 
     * @return void
     */
    private function maybeCreateNotesTable()
    {
        global $wpdb;
        $tableName = $wpdb->prefix . 'fbm_notes';
        
        $charsetCollate = $wpdb->get_charset_collate();
        
        $tableExists = $wpdb->get_var($wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $tableName
        ));
        
        if ($tableExists === $tableName) {
            return; // Table already exists
        }
        
        $sql = "CREATE TABLE {$tableName} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            board_id INT UNSIGNED NOT NULL,
            title VARCHAR(255) NULL,
            content LONGTEXT NOT NULL,
            created_by BIGINT UNSIGNED NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            PRIMARY KEY (id),
            KEY board_id (board_id),
            KEY created_by (created_by)
        ) {$charsetCollate};";
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
}

