<template>
  <div class="fbfm-container min-h-screen bg-white p-6">
    <!-- Header with Configure Button -->
    <div class="fbfm-header flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Focus Mode</h1>
      <button 
        @click="showConfigDialog = true"
        class="bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors border border-gray-200 flex items-center gap-2"
        title="Configure view"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <span>Configure</span>
      </button>
    </div>
    
    <div class="fbfm-grid grid grid-cols-3 gap-6">
      <!-- Left Column -->
      <div class="fbfm-left-col space-y-6">
        <!-- Pomodoro Timer Card -->
        <div v-if="viewConfig.pomodoro" class="fbfm-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg">
          <div class="fbfm-card-header flex items-center gap-2 mb-6">
            <div class="fbfm-alarm-icon">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" fill="#ef4444" stroke="#dc2626" stroke-width="1"/>
                <circle cx="12" cy="12" r="8" fill="white"/>
                <line x1="12" y1="12" x2="12" y2="8" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/>
                <line x1="12" y1="12" x2="14" y2="14" stroke="#ef4444" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M12 4 L12 2 M12 22 L12 20 M4 12 L2 12 M22 12 L20 12" stroke="#ef4444" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
            </div>
            <h2 class="text-gray-900 text-lg font-bold">POMODORO</h2>
          </div>
          
          <!-- Mode Selection -->
          <div class="fbfm-mode-buttons flex gap-2 mb-6">
            <button 
              v-for="mode in pomodoroModes" 
              :key="mode.id"
              @click="selectedPomodoroMode = mode.id"
              :class="[
                'flex-1 py-2.5 px-4 rounded-lg font-medium transition-colors border',
                selectedPomodoroMode === mode.id 
                  ? 'bg-gray-800 text-white border-gray-800' 
                  : 'bg-white text-gray-700 border-gray-200 hover:border-gray-300'
              ]"
            >
              {{ mode.label }}
            </button>
          </div>
          
          <!-- Timer Display -->
          <div class="fbfm-timer-display bg-gradient-to-br from-purple-600 via-purple-500 to-pink-500 rounded-xl p-12 mb-6 text-center shadow-lg">
            <div class="text-7xl font-bold text-white tracking-tight">{{ formatTime(timerSeconds) }}</div>
          </div>
          
          <!-- Timer Controls -->
          <div class="fbfm-timer-controls flex items-center justify-center gap-3">
            <button 
              @click="toggleTimer"
              class="bg-gray-800 text-white px-10 py-3 rounded-lg font-medium hover:bg-gray-900 transition-colors shadow-sm"
            >
              {{ timerRunning ? 'Pause' : 'Start' }}
            </button>
            <button 
              @click="resetTimer"
              class="text-gray-700 hover:text-gray-900 transition-colors p-2"
              title="Reset"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Quick-Action Menu -->
        <div v-if="viewConfig.quickActions" class="fbfm-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg">
          <div class="fbfm-card-header flex items-center justify-between mb-4">
            <h2 class="text-gray-900 text-lg font-semibold flex items-center gap-2">
              <span>✈️</span>
              QUICK-ACTION
            </h2>
          </div>
          
          <div class="fbfm-quick-actions space-y-3">
            <button class="w-full bg-white hover:bg-gray-50 text-gray-700 py-3 px-4 rounded-lg flex items-center gap-3 transition-colors border border-gray-200">
              <span>📄</span>
              <span>Add New Task</span>
            </button>
            <button class="w-full bg-white hover:bg-gray-50 text-gray-700 py-3 px-4 rounded-lg flex items-center gap-3 transition-colors border border-gray-200">
              <span>📁</span>
              <span>Add New Board</span>
            </button>
            <button class="w-full bg-white hover:bg-gray-50 text-gray-700 py-3 px-4 rounded-lg flex items-center gap-3 transition-colors border border-gray-200">
              <span>🗄️</span>
              <span>Add New Stage</span>
            </button>
          </div>
        </div>

        <!-- Notes Section -->
        <div v-if="viewConfig.notes" class="fbfm-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg">
          <div class="fbfm-card-header flex items-center justify-between mb-4">
            <h2 class="text-gray-900 text-lg font-semibold flex items-center gap-2">
              <span>📝</span>
              NOTES
            </h2>
          <div class="mb-4 flex items-center gap-2">
            <el-select
              id="noteBoardSelect"
              v-model="selectedNoteBoardId"
              placeholder="Select a board"
              class="w-56"
              filterable
              clearable
            >
              <el-option
                v-for="board in boards"
                :key="board.id"
                :label="board.name || board.title || ('Board ' + board.id)"
                :value="board.id"
              />
            </el-select>
          </div>
          </div>
          
          <div class="fbfm-notes-editor">
            <input
              v-model="noteTitle"
              type="text"
              placeholder="Note title"
              class="fbfm-note-title-input w-full mb-4 bg-transparent px-4 py-3 text-gray-900 focus:outline-none"
              style="outline: none;
                box-shadow: none;
                border: none;
                padding: 0;"
              :key="`note-title-${editorKey}`"
            />
            <MarkdownEditor
              :key="`note-editor-${editorKey}`"
              v-model="noteContent"
              placeholder="Type your note description here..."
              :autofocus="false"
              :disable_mention="true"
              :disable_media="false"
              @handleMediaUpload="handleNoteMediaUpload"
            />
            <div class="mt-4 flex justify-end gap-2">
              <button 
                @click="cancelNote"
                class="bg-white hover:bg-gray-50 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors border border-gray-200"
              >
                Cancel
              </button>
              <button 
                @click="saveNote"
                :disabled="!noteContent.trim()"
                class="bg-gray-800 hover:bg-gray-900 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-6 py-2 rounded-lg font-medium transition-colors"
              >
                Save Note
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Right Column -->
      <div class="fbfm-right-col col-span-2 space-y-6">
        <!-- Tasks Section -->
        <div v-if="viewConfig.tasks" class="fbfm-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg">
          <div class="fbfm-card-header flex items-center justify-between mb-4">
            <h2 class="text-gray-900 text-lg font-semibold flex items-center gap-2">
              <span>📄</span>
              TASKS
            </h2>
          </div>
          
          <!-- Task Filters -->
          <div class="fbfm-task-filters flex items-center gap-4 mb-4">
            <button 
              v-for="filter in taskFilters" 
              :key="filter.id"
              @click="selectedTaskFilter = filter.id"
              :class="[
                'flex items-center gap-2 px-4 py-2 rounded-lg transition-colors border',
                selectedTaskFilter === filter.id 
                  ? 'bg-gray-800 text-white border-gray-800' 
                  : 'bg-white text-gray-700 border-gray-200 hover:border-gray-300'
              ]"
            >
              <span>{{ filter.icon }}</span>
              <span>{{ filter.label }}</span>
            </button>
            <span class="text-gray-500 text-sm">1 more...</span>
    </div>

          <!-- Task Actions -->
          <div class="fbfm-task-actions flex items-center justify-end gap-2 mb-4">
            <button class="text-gray-600 hover:text-gray-900 p-2 rounded transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
              </svg>
            </button>
            <button class="text-gray-600 hover:text-gray-900 p-2 rounded transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
              </svg>
            </button>
            <button class="text-gray-600 hover:text-gray-900 p-2 rounded transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
            <button class="bg-gray-800 text-white px-4 py-2 rounded-lg font-medium hover:bg-gray-900 transition-colors flex items-center gap-2">
              New
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
          </div>
          
          <!-- Tasks Table -->
          <div class="fbfm-tasks-table overflow-x-auto">
            <table class="w-full text-left">
              <thead>
                <tr class="border-b border-gray-200">
                  <th class="pb-3 text-gray-600 font-medium text-sm">Task name</th>
                  <th class="pb-3 text-gray-600 font-medium text-sm">Started At</th>
                  <th class="pb-3 text-gray-600 font-medium text-sm">Due At</th>
                  <th class="pb-3 text-gray-600 font-medium text-sm">Related Board</th>
                  <th class="pb-3 text-gray-600 font-medium text-sm">Current status</th>
                </tr>
              </thead>
              <tbody>
                <tr 
                  v-for="task in tasks" 
                  :key="task.id" 
                  class="border-b border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors"
                  @click="openTask(task)"
                >
                  <td class="py-4 text-gray-900">{{ task.name }}</td>
                  <td class="py-4 text-gray-600">
                    {{ task.started_at ? dayjs(task.started_at).format('MMM D, YYYY') : '-' }}
                  </td>
                  <td class="py-4 text-gray-600">
                    {{ task.due_at ? dayjs(task.due_at).format('MMM D, YYYY') : '-' }}
                  </td>
                  <td class="py-4 text-gray-600">{{ task.board_title }}</td>
                  <td class="py-4 text-gray-600">{{ task.status }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div class="mt-4">
            <a href="#" class="text-gray-600 hover:text-gray-900 transition-colors">+ New task</a>
          </div>
        </div>
        
        <!-- Boards Section -->
        <div v-if="viewConfig.boards" class="fbfm-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg">
          <div class="fbfm-card-header flex items-center justify-between mb-4">
            <h2 class="text-gray-900 text-lg font-semibold flex items-center gap-2">
              <span>📁</span>
              BOARDS
            </h2>
            <div class="flex items-center gap-4">
              <button class="bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2 text-sm transition-colors border border-gray-200">
                <span>📄</span>
                Boards
              </button>
              <div class="flex items-center gap-2">
                <button class="text-gray-600 hover:text-gray-900 p-2 rounded transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                  </svg>
                </button>
                <button class="text-gray-600 hover:text-gray-900 p-2 rounded transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                  </svg>
                </button>
                <button class="text-gray-600 hover:text-gray-900 p-2 rounded transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </button>
                <button class="bg-gray-800 text-white px-4 py-2 rounded-lg font-medium hover:bg-gray-900 transition-colors flex items-center gap-2">
                  New
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
          
          <!-- Board Cards -->
          <div class="fbfm-boards-grid grid grid-cols-2 gap-4 mb-4">
            <div 
              v-for="board in boards" 
              :key="board.id"
              class="bg-gray-100 border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-200 transition-colors"
              @click="openBoard(board)"
            >
              <div class="flex items-start justify-between mb-3">
                <h3 class="text-gray-900 font-semibold">{{ board.name }}</h3>
                <button 
                  class="text-gray-600 hover:text-gray-900"
                  @click.stop
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                  </svg>
                </button>
              </div>
              <div class="space-y-2 text-sm text-gray-600">
                <div>Tasks to do = {{ board.tasksToDo }}</div>
                <div>✓ Tasks completed = {{ board.tasksDone }}</div>
                <div>Total related tasks = {{ board.totalTasks }}</div>
              </div>
            </div>
          </div>
          
          <div class="flex justify-end">
            <a href="#" class="text-gray-600 hover:text-gray-900 transition-colors">+ New board</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Notes List Section -->
    <div v-if="viewConfig.notesList" class="fbfm-notes-list-section mt-6">
      <div class="fbfm-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg fbfm-notes-list-card">
        <div class="fbfm-card-header flex items-center justify-between mb-4">
          <h2 class="text-gray-900 text-lg font-semibold flex items-center gap-2">
            <span>📋</span>
            MY NOTES
          </h2>
          <div class="flex items-center gap-2">
            <button @click="fetchNotes" class="text-gray-600 hover:text-gray-900 p-2 rounded transition-colors"
              title="Refresh notes">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Notes Grid -->
        <div v-if="notes.length > 0" class="fbfm-notes-grid-container">
          <div class="fbfm-notes-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="note in notes" :key="note.id"
            class="fbfm-note-card bg-gray-50 border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
            @click="openNoteDialog(note)">
            <div class="mb-3">
              <h3 class="text-gray-900 font-semibold text-base">{{ note.title || 'Untitled Note' }}</h3>
              <p v-if="note.board_name" class="text-gray-500 text-xs mt-1">{{ note.board_name }}</p>
            </div>

            <div class="fbfm-note-content feed_texts feed_md_content text-gray-700 text-sm">
              <div v-html="parseMessage(note.content)" class="line-clamp-3"></div>
            </div>
          </div>
        </div>
        </div>
        
        <!-- Empty State -->
        <div v-else class="fbfm-notes-empty text-center py-12 fbfm-notes-grid-container">
          <div class="text-gray-400 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <p class="text-gray-500 text-lg font-medium mb-2">No notes yet</p>
          <p class="text-gray-400 text-sm">Create your first note using the form above</p>
        </div>
      </div>
    </div>

    <!-- Calendar Section -->
    <div v-if="viewConfig.calendar" class="fbfm-calendar-section mt-6">
      <div class="fbfm-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg">
        <!-- Calendar Header -->
        <div class="fbs-calendar-view-header flex items-center justify-between mb-6">
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
              <span>📅</span>
              <h2 class="text-gray-900 text-lg font-semibold">WEEKLY-MONTHLY-CALENDAR</h2>
            </div>
            <div class="fbs-calendar-view-header-actions flex items-center gap-2 ml-6">
              <button
                :class="[
                  'px-4 py-2 rounded-lg text-sm font-medium transition-colors border',
                  currentView === 'month' 
                    ? 'bg-gray-800 text-white border-gray-800' 
                    : 'bg-white text-gray-700 border-gray-200 hover:border-gray-300'
                ]"
                @click="toggleView('month')"
              >
                Month
              </button>
          <button 
                :class="[
                  'px-4 py-2 rounded-lg text-sm font-medium transition-colors border',
                  currentView === 'week' 
                    ? 'bg-gray-800 text-white border-gray-800' 
                    : 'bg-white text-gray-700 border-gray-200 hover:border-gray-300'
                ]"
                @click="toggleView('week')"
              >
                Week
              </button>
            </div>
          </div>
          
          <div class="flex items-center gap-4">
            <div class="fbs-calendar-view-header-date text-gray-900 font-medium">
              {{ currentWeek }}
            </div>
            <div class="flex items-center gap-2">
              <button @click="changeCalendarDate('prev')" class="text-gray-600 hover:text-gray-900 p-2 rounded transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </button>
              <button @click="changeCalendarDate('today')" class="bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors border border-gray-200">
                Today
              </button>
              <button @click="changeCalendarDate('next')" class="text-gray-600 hover:text-gray-900 p-2 rounded transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
          </button>
            </div>
          </div>
        </div>
        
        <!-- Month View -->
        <ElCalendar 
          v-if="isCalendarReady && currentView === 'month'"
          :key="calendarKey"
          class="fbs-calendar-view-calendar"
          ref="calendarView"
        >
          <template #header="{date}">
            <span>{{ getCalendarDate(date) }}</span>
          </template>
          <template #date-cell="{ data }">
            <div class="fbs-calendar-view-cell" @click.stop>
              <div class="fbfm-calendar-tasks">
                <div 
                  v-for="task in getTasksForDay(data.day)" 
                  :key="task.id"
                  class="fbfm-calendar-task-item"
                  :style="{
                    background: task.isOverdue ? 'rgb(245 108 107 / 30%)' : task.status == 'closed' ? 'rgb(23 152 119 / 30%)' : 'rgb(98 104 241 / 30%)'
                  }"
                >
                  <span class="fbfm-calendar-task-title">{{ taskTitle(task.title) }}</span>
                </div>
              </div>
              <div class="fbs-cal-date">
                <span>{{ getDateOnly(data.day) }}</span>
              </div>
              <button
                @click.stop="openCreateTaskDialog(data.day)"
                class="fbs-cal-date-plus-button text-gray-400 hover:text-gray-600 text-xs"
              >
                +
              </button>
            </div>
          </template>
        </ElCalendar>
        
        <!-- Week View -->
        <div v-else-if="currentView === 'week'" class="fbs-calendar-week-view">
          <div class="fbfm-week-view-grid grid grid-cols-7 gap-4">
            <div 
              v-for="day in weekDays" 
              :key="day.date"
              class="fbfm-week-day-column"
            >
              <div class="fbfm-week-day-header mb-4">
                <div class="text-sm font-semibold text-gray-900">{{ day.day }}</div>
                <div class="text-xs text-gray-600">{{ titleDate(day.date) }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ day.tasks.length }} tasks</div>
              </div>
              <div class="fbfm-week-day-tasks space-y-2">
                <div 
                  v-for="task in day.tasks" 
                  :key="task.id"
                  class="fbfm-week-task-item p-2 rounded text-sm"
                  :style="{
                    background: task.isOverdue ? 'rgb(245 108 107 / 30%)' : task.status == 'closed' ? 'rgb(23 152 119 / 30%)' : 'rgb(98 104 241 / 30%)'
                  }"
                >
                  {{ taskTitle(task.title) }}
                </div>
                <button 
                  @click="showTaskAddAtBottom(day.date)"
                  class="w-full text-left text-xs text-gray-500 hover:text-gray-700 p-2 border border-dashed border-gray-300 rounded hover:border-gray-400 transition-colors"
                >
                  + Add Task
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Note View/Edit Dialog -->
    <ElDialog
      v-model="showNoteDialog"
      :title="isEditMode ? 'Edit Note' : 'View Note'"
      width="800px"
      class="fbfm-note-dialog"
      @close="closeNoteDialog"
    >
      <div v-if="currentNote" class="fbfm-note-dialog-content">
        <!-- View Mode -->
        <div v-if="!isEditMode" class="fbfm-note-view-mode">
          <div class="fbfm-note-view-header">
            <h2 v-if="currentNote.title" class="fbfm-note-view-title">{{ currentNote.title }}</h2>
            <div class="fbfm-note-view-actions">
              <button
                @click="isEditMode = true"
                class="fbfm-edit-note-btn"
                title="Edit note"
              >
                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                  <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.179z"/>
                </svg>
              </button>
              <button
                @click="deleteNoteFromDialog(currentNote.id)"
                class="fbfm-delete-note-btn"
                title="Delete note"
              >
                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                  <path d="M5.5 5.5A.5.5 0 016 6v6a.5.5 0 01-1 0V6a.5.5 0 01.5-.5zm2.5 0a.5.5 0 01.5.5v6a.5.5 0 01-1 0V6a.5.5 0 01.5-.5zm3 .5a.5.5 0 00-1 0v6a.5.5 0 001 0V6z"/>
                  <path fill-rule="evenodd" d="M14.5 3a1 1 0 01-1 1H13v9a2 2 0 01-2 2H5a2 2 0 01-2-2V4h-.5a1 1 0 01-1-1V2a1 1 0 011-1H6a1 1 0 011-1h2a1 1 0 011 1h3.5a1 1 0 011 1v1zM4.118 4L4 4.059V13a1 1 0 001 1h6a1 1 0 001-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                </svg>
              </button>
            </div>
          </div>
          <div class="fbfm-note-view-body feed_texts feed_md_content" v-html="parseMessage(currentNote.content)"></div>
        </div>

        <!-- Edit Mode -->
        <div v-else class="fbfm-note-edit-mode">
          <div class="fbfm-note-edit-form">
            <input
              v-model="editNoteTitle"
              type="text"
              placeholder="Note title"
              class="fbfm-note-title-input w-full mb-4 bg-transparent px-4 py-3 text-gray-900 focus:outline-none"
              style="outline: none;
                box-shadow: none;
                border: none;
                padding: 0;"
              :key="`edit-note-title-${editorKey}`"
            />
            <div class="fbfm-markdown-editor-wrapper">
              <MarkdownEditor
                :key="`edit-note-editor-${editorKey}`"
                v-model="editNoteContent"
                placeholder="Type your note description here..."
                :autofocus="true"
                :disable_mention="true"
                :disable_media="false"
                @handleMediaUpload="handleNoteMediaUpload"
              />
            </div>
            <div class="mt-4 flex justify-end gap-2">
              <button 
                @click="cancelEdit"
                class="bg-white hover:bg-gray-50 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors border border-gray-200"
              >
                Cancel
              </button>
              <button 
                @click="updateNote"
                :disabled="!editNoteContent.trim() || savingNote"
                class="bg-gray-800 hover:bg-gray-900 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-6 py-2 rounded-lg font-medium transition-colors"
              >
                Save Changes
              </button>
            </div>
          </div>
        </div>
      </div>
    </ElDialog>

    <!-- Configuration Dialog -->
    <div v-if="showConfigDialog" class="fbfm-config-overlay fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showConfigDialog = false">
      <div class="fbfm-config-dialog bg-white rounded-xl shadow-2xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold text-gray-900">Configure View</h2>
          <button 
            @click="showConfigDialog = false"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <div class="space-y-4">
          <div class="text-sm text-gray-600 mb-4">
            Select which sections you want to display in Focus Mode:
          </div>
          
          <div class="space-y-3">
            <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
              <div class="flex items-center gap-3">
                <span class="text-lg">⏱️</span>
                <div>
                  <div class="font-medium text-gray-900">Pomodoro Timer</div>
                  <div class="text-sm text-gray-500">Focus timer with different modes</div>
                </div>
              </div>
              <input 
                type="checkbox" 
                v-model="viewConfig.pomodoro"
                class="w-5 h-5 text-gray-800 rounded focus:ring-gray-800"
              />
            </label>
            
            <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
              <div class="flex items-center gap-3">
                <span class="text-lg">✈️</span>
                <div>
                  <div class="font-medium text-gray-900">Quick Actions</div>
                  <div class="text-sm text-gray-500">Quick access to common actions</div>
                </div>
              </div>
              <input 
                type="checkbox" 
                v-model="viewConfig.quickActions"
                class="w-5 h-5 text-gray-800 rounded focus:ring-gray-800"
              />
            </label>
            
            <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
              <div class="flex items-center gap-3">
                <span class="text-lg">📝</span>
                <div>
                  <div class="font-medium text-gray-900">Notes Editor</div>
                  <div class="text-sm text-gray-500">Create and edit notes</div>
                </div>
              </div>
              <input 
                type="checkbox" 
                v-model="viewConfig.notes"
                class="w-5 h-5 text-gray-800 rounded focus:ring-gray-800"
              />
            </label>
            
            <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
              <div class="flex items-center gap-3">
                <span class="text-lg">📄</span>
                <div>
                  <div class="font-medium text-gray-900">Tasks</div>
                  <div class="text-sm text-gray-500">View and manage your tasks</div>
                </div>
              </div>
              <input 
                type="checkbox" 
                v-model="viewConfig.tasks"
                class="w-5 h-5 text-gray-800 rounded focus:ring-gray-800"
              />
            </label>
            
            <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
              <div class="flex items-center gap-3">
                <span class="text-lg">📁</span>
                <div>
                  <div class="font-medium text-gray-900">Boards</div>
                  <div class="text-sm text-gray-500">View your boards and statistics</div>
                </div>
              </div>
              <input 
                type="checkbox" 
                v-model="viewConfig.boards"
                class="w-5 h-5 text-gray-800 rounded focus:ring-gray-800"
              />
            </label>
            
            <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
              <div class="flex items-center gap-3">
                <span class="text-lg">📋</span>
                <div>
                  <div class="font-medium text-gray-900">Notes List</div>
                  <div class="text-sm text-gray-500">View all your saved notes</div>
                </div>
              </div>
              <input 
                type="checkbox" 
                v-model="viewConfig.notesList"
                class="w-5 h-5 text-gray-800 rounded focus:ring-gray-800"
              />
            </label>
            
            <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
              <div class="flex items-center gap-3">
                <span class="text-lg">📅</span>
                <div>
                  <div class="font-medium text-gray-900">Calendar</div>
                  <div class="text-sm text-gray-500">Monthly and weekly calendar view</div>
                </div>
              </div>
              <input 
                type="checkbox" 
                v-model="viewConfig.calendar"
                class="w-5 h-5 text-gray-800 rounded focus:ring-gray-800"
              />
            </label>
          </div>
        </div>
        
        <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
          <button 
            @click="resetViewConfig"
            class="bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors border border-gray-200"
          >
            Reset to Default
          </button>
          <button 
            @click="saveViewConfig"
            class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2 rounded-lg font-medium transition-colors"
          >
            Save Configuration
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import { ElCalendar, ElSelect, ElOption, ElDialog } from 'element-plus'
import MarkdownEditor from './MilkDownEditor/MarkdownEditor.vue'
import Rest from '@/Bits/Rest.js'

dayjs.extend(relativeTime)
dayjs.extend(utc)
dayjs.extend(timezone)

// Pomodoro Timer
const pomodoroModes = [
  { id: 'pomodoro', label: 'Pomodoro', minutes: 25 },
  { id: 'short-break', label: 'Short Break', minutes: 5 },
  { id: 'long-break', label: 'Long Break', minutes: 15 }
]

const selectedPomodoroMode = ref('pomodoro')
const timerSeconds = ref(25 * 60) // 25 minutes in seconds
const timerRunning = ref(false)
let timerInterval = null

const currentMode = computed(() => {
  return pomodoroModes.find(m => m.id === selectedPomodoroMode.value)
})

const formatTime = (seconds) => {
  const mins = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
}

const toggleTimer = () => {
  timerRunning.value = !timerRunning.value
  
  if (timerRunning.value) {
    timerInterval = setInterval(() => {
      if (timerSeconds.value > 0) {
        timerSeconds.value--
      } else {
        timerRunning.value = false
        clearInterval(timerInterval)
        // Timer completed - could add notification here
      }
    }, 1000)
  } else {
    if (timerInterval) {
      clearInterval(timerInterval)
    }
  }
}

// Reset timer when mode changes
const resetTimer = () => {
  timerSeconds.value = currentMode.value.minutes * 60
  timerRunning.value = false
  if (timerInterval) {
    clearInterval(timerInterval)
    timerInterval = null
  }
}

// Task Filters
const taskFilters = [
  { id: 'today', label: 'Today Tasks', icon: '📄📄' },
  { id: 'all', label: 'All Tasks', icon: '📄' }
]

const selectedTaskFilter = ref('all')

// Tasks Data
const tasks = ref([
  {
    id: 1,
    name: 'Reading Books',
    board_title: 'Personal Goal',
    status: 'In Progress'
  },
  {
    id: 2,
    name: 'Hello world',
    board_title: '-',
    status: 'Pending'
  }
])

// Boards Data (will be loaded from backend)
const boards = ref([])
const loadingBoards = ref(false)

// Calendar
const currentView = ref('month')
const currentDate = ref(dayjs())
const currentHeaderDate = ref('')
const weekDays = ref([])
const calendarKey = ref(0)
const isCalendarReady = ref(false)
const showTaskAddDate = ref(null)
const showTaskAddBottom = ref(false)
const startOfWeek = ref(0) // Will be set from WordPress settings

// Calendar tasks (from the tasks data)
const calendarTasks = computed(() => {
  return tasks.value.map(task => ({
    ...task,
    started_at: task.started_at || null,
    due_at: task.due_at || null,
    isOverdue: false, // You can calculate this based on due_at
    status: task.status || 'open'
  }))
})

const currentWeek = computed(() => {
  if (currentView.value === 'week') {
    const startOfWeekDate = dayjs(currentDate.value).startOf('week')
    const endOfWeekDate = startOfWeekDate.add(6, 'day')
    return formatWeekRange(startOfWeekDate.toDate(), endOfWeekDate.toDate())
  } else {
    return currentHeaderDate.value || dayjs(currentDate.value).format('MMMM YYYY')
  }
})

const getTasksForDay = (day) => {
  const formattedDay = dayjs(day).format('YYYY-MM-DD')
  let tasksHaveStarts = calendarTasks.value.filter((task) => {
    if (!task.started_at) return false
    return dayjs(task.started_at).format('YYYY-MM-DD') === formattedDay
  })
  let tasksHaveDues = calendarTasks.value.filter((task) => {
    if (!task.due_at) return false
    const startDate = task.started_at ? dayjs(task.started_at).format('YYYY-MM-DD') : null
    const dueDate = dayjs(task.due_at).format('YYYY-MM-DD')
    if (startDate !== dueDate) {
      return dueDate === formattedDay
    }
    return false
  })
  return [...tasksHaveStarts, ...tasksHaveDues]
}

const getCalendarDate = (date) => {
  currentHeaderDate.value = date
  return date
}

const getDateOnly = (date) => {
  return dayjs(date).format('D')
}

const taskTitle = (title) => {
  return title && title.length > 50 ? title.substring(0, 50) + '...' : title
}

const changeCalendarDate = (period) => {
  if (currentView.value === 'month') {
    if (calendarView.value) {
      const periodStr = period === 'today' ? period : period + '-month'
      calendarView.value.selectDate(periodStr)
    }
  } else {
    switch (period) {
      case 'today':
        currentDate.value = dayjs().startOf('day')
        break
      case 'next':
        currentDate.value = dayjs(currentDate.value).add(7, 'day')
        break
      case 'prev':
        currentDate.value = dayjs(currentDate.value).subtract(7, 'day')
        break
    }
    setCurrentWeekDays()
  }
}

const toggleView = (type = 'month') => {
  currentView.value = type
  
  if (currentView.value === 'week') {
    currentDate.value = dayjs().startOf('day')
    setCurrentWeekDays()
  } else {
    calendarKey.value += 1
  }
}

const setCurrentWeekDays = () => {
  const daysOfWeekNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
  
  // Rotate the array based on start of week setting
  const rotatedDays = [...daysOfWeekNames]
  for (let i = 0; i < startOfWeek.value; i++) {
    rotatedDays.push(rotatedDays.shift())
  }

  const currentDateObj = dayjs(currentDate.value)
  let startOfWeekDate = currentDateObj.startOf('week')
  if (startOfWeek.value > 0) {
    const currentDay = currentDateObj.day()
    const daysToSubtract = (currentDay < startOfWeek.value) 
      ? (7 - (startOfWeek.value - currentDay)) 
      : (currentDay - startOfWeek.value)
    startOfWeekDate = currentDateObj.subtract(daysToSubtract, 'day')
  }

  weekDays.value = Array.from({ length: 7 }).map((_, dayOffset) => {
    const currentDay = startOfWeekDate.add(dayOffset, 'day')
    const formattedDate = currentDay.format('YYYY-MM-DD')

    // Filter tasks that either start or end on the current date
    const tasksForCurrentDay = calendarTasks.value.filter(task => {
      const startDate = task.started_at ? dayjs(task.started_at).format('YYYY-MM-DD') : null
      const endDate = task.due_at ? dayjs(task.due_at).format('YYYY-MM-DD') : null
      return startDate === formattedDate || endDate === formattedDate
    }).map(task => ({
      ...task,
      dayTaskType: task.started_at && dayjs(task.started_at).format('YYYY-MM-DD') === formattedDate ? 'start' : 'end',
    }))

    return {
      day: rotatedDays[dayOffset],
      date: formattedDate,
      tasks: tasksForCurrentDay,
    }
  })
}

const formatWeekRange = (startOfWeek, endOfWeek) => {
  const startMonth = dayjs(startOfWeek).format('MMM')
  const startDay = dayjs(startOfWeek).format('D')
  const endMonth = dayjs(endOfWeek).format('MMM')
  const endDay = dayjs(endOfWeek).format('D')
  const startYear = dayjs(startOfWeek).format('YYYY')
  const endYear = dayjs(endOfWeek).format('YYYY')

  if (startYear === endYear) {
    if (startMonth === endMonth) {
      return `${startMonth} ${startDay} — ${endDay}`
    } else {
      return `${startMonth} ${startDay} — ${endMonth} ${endDay}`
    }
  } else {
    return `${startMonth} ${startDay}, ${startYear} — ${endMonth} ${endDay}, ${endYear}`
  }
}

const openCreateTaskDialog = (day) => {
  console.log('Open create task dialog for:', day)
  // Handle task creation
}

const showTaskAddAtBottom = (date) => {
  showTaskAddDate.value = date
  showTaskAddBottom.value = true
}

const titleDate = (date) => {
  return dayjs(date).format('MMM D')
}

const calendarView = ref(null)

// Notes
const noteTitle = ref('')
const noteContent = ref('')
const notes = ref([])
const selectedNoteBoardId = ref(null)
const editorKey = ref(0)
const showNoteDialog = ref(false)
const currentNote = ref(null)
const isEditMode = ref(false)
const editNoteTitle = ref('')
const editNoteContent = ref('')
const savingNote = ref(false)

// View Configuration
const showConfigDialog = ref(false)
const defaultViewConfig = {
  pomodoro: true,
  quickActions: true,
  notes: true,
  tasks: true,
  boards: true,
  notesList: true,
  calendar: true
}

const viewConfig = ref({ ...defaultViewConfig })

// Load view configuration from localStorage
const loadViewConfig = () => {
  try {
    const saved = localStorage.getItem('fbfm_view_config')
    if (saved) {
      const parsed = JSON.parse(saved)
      viewConfig.value = { ...defaultViewConfig, ...parsed }
    }
  } catch (error) {
    console.error('Failed to load view config:', error)
  }
}

// Save view configuration to localStorage
const saveViewConfig = () => {
  try {
    localStorage.setItem('fbfm_view_config', JSON.stringify(viewConfig.value))
    showConfigDialog.value = false
  } catch (error) {
    console.error('Failed to save view config:', error)
  }
}

// Reset view configuration to default
const resetViewConfig = () => {
  viewConfig.value = { ...defaultViewConfig }
  try {
    localStorage.removeItem('fbfm_view_config')
  } catch (error) {
    console.error('Failed to reset view config:', error)
  }
}

const saveNote = async () => {
  if (!noteContent.value.trim()) {
    return
  }
  
  try {
    const response = await Rest.post('notes', {
      title: noteTitle.value.trim() || null,
      content: noteContent.value,
      board_id: selectedNoteBoardId.value
    })
    
    if (response.data) {
      const boardName = boards.value.find(b => b.id === selectedNoteBoardId.value)?.name || null
      notes.value.unshift({
        id: response.data.id,
        title: response.data.title || null,
        content: response.data.content,
        board_id: selectedNoteBoardId.value,
        board_name: boardName,
        createdAt: response.data.created_at,
        updatedAt: response.data.updated_at
      })
      // Clear editor and title after successful save
      noteTitle.value = ''
      noteContent.value = ''
      selectedNoteBoardId.value = null
      // Force editor reset by incrementing key
      editorKey.value++
    }
  } catch (error) {
    console.error('Failed to save note:', error)
  }
}

const cancelNote = () => {
  noteTitle.value = ''
  noteContent.value = ''
  // Force editor reset by incrementing key
  editorKey.value++
}

const fetchNotes = async () => {
  try {
    // Fetch all notes (no board_id parameter to get notes from all boards)
    const response = await Rest.get('notes')
    
    if (response.data && Array.isArray(response.data)) {
      notes.value = response.data.map(note => ({
        id: note.id,
        title: note.title || null,
        content: note.content || note.description || '',
        board_id: note.board_id,
        board_name: note.board_name || null,
        createdAt: note.created_at,
        updatedAt: note.updated_at
      }))
    } else {
      notes.value = []
    }
  } catch (error) {
    console.error('Failed to fetch notes:', error)
    notes.value = []
  }
}

const openNoteDialog = (note) => {
  currentNote.value = { ...note }
  editNoteTitle.value = note.title || ''
  editNoteContent.value = note.content || ''
  isEditMode.value = false
  showNoteDialog.value = true
}

const closeNoteDialog = () => {
  showNoteDialog.value = false
  isEditMode.value = false
  currentNote.value = null
  editNoteTitle.value = ''
  editNoteContent.value = ''
}

const cancelEdit = () => {
  isEditMode.value = false
  if (currentNote.value) {
    editNoteTitle.value = currentNote.value.title || ''
    editNoteContent.value = currentNote.value.content || ''
  }
}

const updateNote = async () => {
  if (!editNoteContent.value.trim()) {
    return
  }

  if (!currentNote.value) {
    return
  }

  savingNote.value = true
  try {
    const response = await Rest.patch(`notes/${currentNote.value.id}`, {
      title: editNoteTitle.value.trim() || null,
      content: editNoteContent.value.trim()
    })
    
    if (response.data) {
      // Update the note in the list
      const index = notes.value.findIndex(n => n.id === currentNote.value.id)
      if (index !== -1) {
        notes.value[index] = {
          ...notes.value[index],
          title: response.data.title || '',
          content: response.data.content || response.data.description || '',
          updatedAt: response.data.updated_at || ''
        }
      }
      
      // Update current note
      currentNote.value = {
        ...currentNote.value,
        title: response.data.title || '',
        content: response.data.content || response.data.description || '',
        updatedAt: response.data.updated_at || ''
      }
      
      // Update edit fields
      editNoteTitle.value = response.data.title || ''
      editNoteContent.value = response.data.content || response.data.description || ''
      
      isEditMode.value = false
      editorKey.value++
    }
  } catch (error) {
    console.error('Error updating note:', error)
  } finally {
    savingNote.value = false
  }
}

const deleteNoteFromDialog = async (noteId) => {
  if (!confirm('Are you sure you want to delete this note?')) {
    return
  }
  
  try {
    await Rest.delete(`notes/${noteId}`)
    notes.value = notes.value.filter(note => note.id !== noteId)
    if (showNoteDialog.value) {
      closeNoteDialog()
    }
  } catch (error) {
    console.error('Failed to delete note:', error)
  }
}

const processInlineMarkdown = (content) => {
  // Process inline markdown elements (links, bold, italic) in headings
  let processed = content
  // Links first
  processed = processed.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2">$1</a>')
  // Then bold
  processed = processed.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
  // Then italic
  processed = processed.replace(/\*(.*?)\*/g, '<em>$1</em>')
  return processed
}

const mdToHtml = (text) => {
  if (!text) {
    return ''
  }
  
  // Hide markdown empty content
  text = text.replace('&#x20;', '')
  
  // Convert code blocks first (to preserve content)
  text = text.replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>')
  
  // Convert inline code (single backticks, but not inside code blocks)
  text = text.replace(/`([^`\n]+)`/g, '<code>$1</code>')
  
  // Convert links [text](url) - before bold/italic to handle nested markdown
  text = text.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2">$1</a>')
  
  // Convert bold and italic (order matters - bold first, but after links)
  text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
  text = text.replace(/\*(.*?)\*/g, '<em>$1</em>')
  
  // Convert strikethrough
  text = text.replace(/\~\~(.*?)\~\~/g, '<del>$1</del>')
  
  // Convert plain URLs to clickable links
  text = text.replace(/<(https?:\/\/[^\s>]+)>/g, '<a href="$1">$1</a>')
  
  // Convert headings - process content inside headings recursively
  text = text.replace(/^####\s+(.*?)$/gm, (match, content) => {
    return '<h4>' + processInlineMarkdown(content) + '</h4>'
  })
  text = text.replace(/^###\s+(.*?)$/gm, (match, content) => {
    return '<h3>' + processInlineMarkdown(content) + '</h3>'
  })
  text = text.replace(/^##\s+(.*?)$/gm, (match, content) => {
    return '<h2>' + processInlineMarkdown(content) + '</h2>'
  })
  text = text.replace(/^#\s+(.*?)$/gm, (match, content) => {
    return '<h1>' + processInlineMarkdown(content) + '</h1>'
  })
  
  // Convert blockquotes
  text = text.replace(/^>\s+(.*?)$/gm, '<blockquote>$1</blockquote>')
  
  // Convert bullet lists
  text = text.replace(/^[\*\-]\s+(.*)$/gm, '<li>$1</li>')
  // Wrap consecutive list items in <ul>
  text = text.replace(/(<li>.*?<\/li>(?:\n<li>.*?<\/li>)*)/g, function(match) {
    if (match.includes('<ul>') || match.includes('<ol>')) {
      return match // Already wrapped
    }
    return '<ul>' + match.replace(/\n/g, '') + '</ul>'
  })
  
  // Convert numbered lists
  text = text.replace(/^\d+\.\s+(.*)$/gm, '<li>$1</li>')
  // Wrap consecutive numbered list items in <ol>
  text = text.replace(/(<li>.*?<\/li>(?:\n<li>.*?<\/li>)*)/g, function(match) {
    if (match.includes('<ul>') || match.includes('<ol>')) {
      return match // Already wrapped
    }
    return '<ol>' + match.replace(/\n/g, '') + '</ol>'
  })
  
  // Convert line breaks (but preserve them in code blocks)
  text = text.replace(/\n/g, '<br>')
  
  return text
}

const parseMessage = (message) => {
  if (!message) {
    return ''
  }

  // First convert markdown to HTML
  let html = mdToHtml(message)

  // Then process links and hashtags
  let newMessage = html.replace(
    /<a\s(?:(?!target="_blank")[^>])*>|<[^>]+>|#([a-zA-Z0-9_-]+)/gi,
    (match, hashtag, offset, string) => {
      // Case 1: It's an <a> tag without target="_blank"
      if (match.startsWith('<a') && !match.includes('target="_blank"')) {
        const isSameDomain = match.includes(window.location.origin)
        return isSameDomain
          ? match
          : match.replace('<a', '<a target="_blank" rel="noopener noreferrer nofollow"')
      }

      // Case 2: It's a hashtag (and not inside an HTML tag)
      if (hashtag && !isInsideHtmlTag(string, offset)) {
        return `<a href="#" onclick="return false;">#${hashtag}</a>`
      }

      // Case 3: It's any other HTML tag or a hashtag inside a tag
      return match
    }
  )

  return newMessage
}

const isInsideHtmlTag = (string, position) => {
  let isInTag = false
  for (let i = 0; i < position; i++) {
    if (string[i] === '<') isInTag = true
    if (string[i] === '>') isInTag = false
  }
  return isInTag
}

// Fetch boards from backend
const fetchBoards = async () => {
  loadingBoards.value = true
  try {
    const response = await Rest.get('boards', {
      type: 'to-do'
    })
    
    // New endpoint returns { boards: [], tasks: [] }
    if (response.boards && Array.isArray(response.boards)) {
      // Transform boards data
      boards.value = response.boards.map(board => ({
        id: board.id,
        name: board.title,
        tasksToDo: board.tasks_to_do || 0,
        tasksDone: board.tasks_done || 0,
        totalTasks: board.tasks_count || 0,
        description: board.description || '',
        background: board.background || '',
        labels: board.labels || []
      }))
    }
    
    // Update tasks if provided separately
    if (response.tasks && Array.isArray(response.tasks)) {
      tasks.value = response.tasks.map(task => ({
        id: task.id,
        name: task.title,
        title: task.title,
        board_id: task.board_id,
        board_title: boards.value.find(b => b.id === task.board_id)?.name || '-',
        status: task.status || 'open',
        started_at: task.started_at || null,
        due_at: task.due_at || null,
        isOverdue: task.due_at ? dayjs(task.due_at).isBefore(dayjs(), 'day') : false
      }))
    }
  } catch (error) {
    console.error('Failed to fetch boards:', error)
    // Keep default boards if fetch fails
    boards.value = [
      {
        id: 1,
        name: 'New Board',
        tasksToDo: 0,
        tasksDone: 0,
        totalTasks: 0
      }
    ]
  } finally {
    loadingBoards.value = false
  }
}

const handleNoteMediaUpload = (file) => {
  // Handle media upload for notes
  console.log('Media upload:', file)
  // You can implement file upload logic here
}

// Helper function to sanitize task title for URL
const sanitizeTaskTitleForUrl = (title) => {
  if (!title) return ''
  return title
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
    .substring(0, 50) // Limit length
}

// Navigate to task
const openTask = (task) => {
  if (!task || !task.id || !task.board_id) {
    return
  }
  
  const url = new URL(window.location.href)
  const sanitizedTitle = sanitizeTaskTitleForUrl(task.title || task.name || '')
  const taskIdName = `${task.id}${sanitizedTitle ? '-' + sanitizedTitle : ''}`
  
  url.searchParams.set('page', 'fluent-boards')
  url.hash = `#/boards/${task.board_id}/tasks/${taskIdName}`
  
  window.location.href = url.toString()
}

// Navigate to board
const openBoard = (board) => {
  if (!board || !board.id) {
    return
  }
  
  const url = new URL(window.location.href)
  url.searchParams.set('page', 'fluent-boards')
  url.hash = `#/boards/${board.id}`
  
  window.location.href = url.toString()
}

// Watch for mode changes
watch(selectedPomodoroMode, () => {
  resetTimer()
})

onMounted(() => {
  // Set active menu item
  if (document.querySelector('.fframe_app')) {
    const menuItems = document.querySelectorAll('.fframe_menu li')
    menuItems.forEach(item => item.classList.remove('active_item'))
    const modesItem = document.querySelector('.fframe_menu li.fframe_item_modes')
    if (modesItem) {
      modesItem.classList.add('active_item')
    }
  }
  
  // Load view configuration
  loadViewConfig()
  
  // Initialize calendar
  calendarKey.value += 1
  setCurrentWeekDays()
  isCalendarReady.value = true
  
  // Fetch boards from backend
  fetchBoards()
  
  // Fetch notes from backend
  fetchNotes()
})

onUnmounted(() => {
  if (timerInterval) {
    clearInterval(timerInterval)
  }
})
</script>

<style scoped>
.fbfm-container {
  background-color: #ffffff;
}

.fbfm-card {
  background-color: #ffffff;
}

.fbfm-grid {
  max-width: 100%;
}

/* Calendar Styles */
.fbs-calendar-view-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.fbs-calendar-view-header-actions {
  display: flex;
  gap: 0.5rem;
}

.fbs-calendar-view-calendar {
  margin-top: 1rem;
}

.fbs-calendar-view-cell {
  position: relative;
  min-height: 80px;
  padding: 0.5rem;
}

.fbfm-calendar-tasks {
  margin-bottom: 0.5rem;
}

.fbfm-calendar-task-item {
  padding: 0.25rem 0.5rem;
  margin-bottom: 0.25rem;
  border-radius: 0.25rem;
  font-size: 0.75rem;
  cursor: pointer;
}

.fbfm-calendar-task-title {
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.fbs-cal-date {
  position: absolute;
  top: 0.5rem;
  left: 0.5rem;
  font-weight: 600;
  color: #374151;
}

.fbs-cal-date-plus-button {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  background: transparent;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
}

.fbs-calendar-week-view {
  margin-top: 1rem;
}

.fbfm-week-view-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 1rem;
}

.fbfm-week-day-column {
  border-right: 1px solid #e5e7eb;
  padding-right: 1rem;
}

.fbfm-week-day-column:last-child {
  border-right: none;
}

.fbfm-week-day-header {
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #e5e7eb;
  margin-bottom: 0.5rem;
}

.fbfm-week-day-tasks {
  min-height: 200px;
}

.fbfm-week-task-item {
  margin-bottom: 0.5rem;
  border-radius: 0.25rem;
  cursor: pointer;
}

/* Pomodoro Timer Styles */
.fbfm-alarm-icon {
  display: flex;
  align-items: center;
  justify-content: center;
}

.fbfm-timer-display {
  background: linear-gradient(135deg, #9333ea 0%, #c026d3 50%, #ec4899 100%);
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.fbfm-timer-controls {
  margin-top: 1rem;
}

.fbfm-timer-controls button {
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Notes Section Styles */
.fbfm-note-title-input {
  font-size: 18px;
  font-weight: bold;
}

.fbfm-note-title-input::placeholder {
  color: #9ca3af;
  font-weight: normal;
}

.fbfm-notes-editor {
  position: relative;
}

/* Notes List Section Styles */
.fbfm-notes-list-section {
  margin-top: 1.5rem;
}

.fbfm-notes-list-card {
  height: 400px;
  display: flex;
  flex-direction: column;
}

.fbfm-notes-grid-container {
  flex: 1;
  overflow-y: auto;
  min-height: 0;
}

.fbfm-notes-grid {
  display: grid;
  gap: 1rem;
}

.fbfm-note-card {
  transition: all 0.2s ease;
}

.fbfm-note-card:hover {
  border-color: #d1d5db;
}

.fbfm-notes-empty {
  padding: 3rem 0;
}

.fbfm-note-content {
  max-height: 100px;
  overflow: hidden;
}

.fbfm-note-content.feed_texts.feed_md_content {
  word-wrap: break-word;
}

.fbfm-note-content.feed_texts.feed_md_content :deep(p) {
  margin: 0 0 8px 0;
}

.fbfm-note-content.feed_texts.feed_md_content :deep(p:last-child) {
  margin-bottom: 0;
}

.fbfm-note-content.feed_texts.feed_md_content :deep(a) {
  color: #2563eb;
  text-decoration: none;
}

.fbfm-note-content.feed_texts.feed_md_content :deep(a:hover) {
  text-decoration: underline;
}

.fbfm-note-content.feed_texts.feed_md_content :deep(strong) {
  font-weight: 600;
}

.fbfm-note-content.feed_texts.feed_md_content :deep(em) {
  font-style: italic;
}

.fbfm-note-content.feed_texts.feed_md_content :deep(code) {
  background: #f3f4f6;
  padding: 2px 4px;
  border-radius: 3px;
  font-family: monospace;
  font-size: 0.9em;
}

.fbfm-note-content.feed_texts.feed_md_content :deep(pre) {
  background: #f3f4f6;
  padding: 12px;
  border-radius: 6px;
  overflow-x: auto;
  margin: 8px 0;
}

.fbfm-note-content.feed_texts.feed_md_content :deep(ul),
.fbfm-note-content.feed_texts.feed_md_content :deep(ol) {
  margin: 8px 0;
  padding-left: 24px;
}

.fbfm-note-content.feed_texts.feed_md_content :deep(li) {
  margin: 4px 0;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Configuration Dialog Styles */
.fbfm-header {
  margin-bottom: 1.5rem;
}

.fbfm-config-overlay {
  z-index: 9999;
}

.fbfm-config-dialog {
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Note Dialog Styles */
.fbfm-note-dialog-content {
  padding: 0;
}

.fbfm-note-view-mode {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.fbfm-note-view-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 16px;
}

.fbfm-note-view-title {
  margin: 0;
  font-size: 24px;
  font-weight: 600;
  color: #111827;
  flex: 1;
}

.fbfm-note-view-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}

.fbfm-edit-note-btn {
  background: transparent;
  border: none;
  color: #2563eb;
  cursor: pointer;
  padding: 8px;
  border-radius: 4px;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.fbfm-edit-note-btn:hover {
  background: #eff6ff;
}

.fbfm-delete-note-btn {
  background: transparent;
  border: none;
  color: #ef4444;
  cursor: pointer;
  padding: 8px;
  border-radius: 4px;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.fbfm-delete-note-btn:hover {
  background: #fee2e2;
}

.fbfm-note-view-body {
  color: #374151;
  font-size: 14px;
  line-height: 1.6;
  min-height: 100px;
}

.fbfm-note-edit-mode {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.fbfm-note-edit-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.fbfm-markdown-editor-wrapper {
  min-height: 300px;
}
</style>

