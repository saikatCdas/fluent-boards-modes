<template>
  <div class="fbfm-container min-h-screen bg-white p-6">
    <div class="fbfm-grid grid grid-cols-3 gap-6">
      <!-- Left Column -->
      <div class="fbfm-left-col space-y-6">
        <!-- Pomodoro Timer Card -->
        <div class="fbfm-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg">
          <div class="fbfm-card-header flex items-center justify-between mb-4">
            <h2 class="text-gray-900 text-lg font-semibold flex items-center gap-2">
              <span>⏰</span>
              POMODORO
            </h2>
          </div>
          
          <!-- Mode Selection -->
          <div class="fbfm-mode-buttons flex gap-2 mb-6">
            <button 
              v-for="mode in pomodoroModes" 
              :key="mode.id"
              @click="selectedPomodoroMode = mode.id"
              :class="[
                'flex-1 py-2 px-4 rounded-lg font-medium transition-colors',
                selectedPomodoroMode === mode.id 
                  ? 'bg-gray-900 text-white' 
                  : 'bg-gray-100 text-gray-900'
              ]"
            >
              {{ mode.label }}
            </button>
          </div>
          
          <!-- Timer Display -->
          <div class="fbfm-timer-display bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl p-8 mb-6 text-center">
            <div class="text-6xl font-bold text-white mb-2">{{ formatTime(timerSeconds) }}</div>
          </div>
          
          <!-- Timer Controls -->
          <div class="fbfm-timer-controls flex items-center justify-center gap-4">
            <button 
              @click="toggleTimer"
              class="bg-gray-900 text-white px-8 py-3 rounded-lg font-medium hover:bg-gray-800 transition-colors"
            >
              {{ timerRunning ? 'Pause' : 'Start' }}
            </button>
            <button class="text-gray-600 hover:text-gray-900 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </button>
            <button class="text-gray-600 hover:text-gray-900 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Quick-Action Menu -->
        <div class="fbfm-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg">
          <div class="fbfm-card-header flex items-center justify-between mb-4">
            <h2 class="text-gray-900 text-lg font-semibold flex items-center gap-2">
              <span>✈️</span>
              QUICK-ACTION
            </h2>
          </div>
          
          <div class="fbfm-quick-actions space-y-3">
            <button class="w-full bg-gray-900 hover:bg-gray-800 text-white py-3 px-4 rounded-lg flex items-center gap-3 transition-colors">
              <span>📄</span>
              <span>Add New Task</span>
            </button>
            <button class="w-full bg-gray-900 hover:bg-gray-800 text-white py-3 px-4 rounded-lg flex items-center gap-3 transition-colors">
              <span>📁</span>
              <span>Add New Project</span>
            </button>
            <button class="w-full bg-gray-900 hover:bg-gray-800 text-white py-3 px-4 rounded-lg flex items-center gap-3 transition-colors">
              <span>🗄️</span>
              <span>Database</span>
            </button>
          </div>
        </div>

        <!-- Notes Section -->
        <div class="fbfm-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg">
          <div class="fbfm-card-header flex items-center justify-between mb-4">
            <h2 class="text-gray-900 text-lg font-semibold flex items-center gap-2">
              <span>📝</span>
              NOTES
            </h2>
          </div>
          
          <div class="fbfm-notes-editor">
            <MarkdownEditor
              v-model="noteContent"
              placeholder="Type your note description here..."
              :autofocus="false"
              :disable_mention="true"
              :disable_media="false"
              @handleMediaUpload="handleNoteMediaUpload"
            />
            <div class="mt-4 flex justify-end gap-2">
              <button 
                @click="saveNote"
                :disabled="!noteContent.trim()"
                class="bg-gray-900 hover:bg-gray-800 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-6 py-2 rounded-lg font-medium transition-colors"
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
        <div class="fbfm-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg">
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
                'flex items-center gap-2 px-4 py-2 rounded-lg transition-colors',
                selectedTaskFilter === filter.id 
                  ? 'bg-gray-900 text-white' 
                  : 'bg-gray-100 text-gray-600 hover:text-gray-900'
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
            <button class="bg-gray-900 text-white px-4 py-2 rounded-lg font-medium hover:bg-gray-800 transition-colors flex items-center gap-2">
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
                  <th class="pb-3 text-gray-600 font-medium text-sm">Start Task</th>
                  <th class="pb-3 text-gray-600 font-medium text-sm">End Task</th>
                  <th class="pb-3 text-gray-600 font-medium text-sm">Related Project</th>
                  <th class="pb-3 text-gray-600 font-medium text-sm">Current status</th>
                  <th class="pb-3 text-gray-600 font-medium text-sm">∑ Time taken</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="task in tasks" :key="task.id" class="border-b border-gray-200">
                  <td class="py-4 text-gray-900">{{ task.name }}</td>
                  <td class="py-4">
                    <button class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                      Start Task
                    </button>
                  </td>
                  <td class="py-4">
                    <button class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                      End Task
                    </button>
                  </td>
                  <td class="py-4 text-gray-600">{{ task.project }}</td>
                  <td class="py-4 text-gray-600">{{ task.status }}</td>
                  <td class="py-4 text-gray-600">{{ task.timeTaken }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div class="mt-4">
            <a href="#" class="text-gray-600 hover:text-gray-900 transition-colors">+ New page</a>
          </div>
        </div>
        
        <!-- Projects Section -->
        <div class="fbfm-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg">
          <div class="fbfm-card-header flex items-center justify-between mb-4">
            <h2 class="text-gray-900 text-lg font-semibold flex items-center gap-2">
              <span>📁</span>
              PROJECTS
            </h2>
            <div class="flex items-center gap-4">
              <button class="bg-gray-900 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm">
                <span>📄</span>
                Projects
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
                <button class="bg-gray-900 text-white px-4 py-2 rounded-lg font-medium hover:bg-gray-800 transition-colors flex items-center gap-2">
                  New
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
          
          <!-- Project Cards -->
          <div class="fbfm-projects-grid grid grid-cols-2 gap-4 mb-4">
            <div 
              v-for="project in projects" 
              :key="project.id"
              class="bg-gray-100 border border-gray-200 rounded-lg p-4"
            >
              <div class="flex items-start justify-between mb-3">
                <h3 class="text-gray-900 font-semibold">{{ project.name }}</h3>
                <button class="text-gray-600 hover:text-gray-900">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                  </svg>
                </button>
              </div>
              <div class="space-y-2 text-sm text-gray-600">
                <div>Tasks to do = {{ project.tasksToDo }}</div>
                <div>✓ Tasks done = {{ project.tasksDone }}</div>
                <div>Total related tasks = {{ project.totalTasks }}</div>
                <div>Total time spent = {{ project.timeSpent }}</div>
              </div>
            </div>
          </div>
          
          <div class="flex justify-end">
            <a href="#" class="text-gray-600 hover:text-gray-900 transition-colors">+ New page</a>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Calendar Section -->
    <div class="fbfm-calendar-section mt-6">
      <div class="fbfm-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg">
        <!-- Calendar Header -->
        <div class="fbfm-calendar-header flex items-center justify-between mb-6">
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
              <span>📅</span>
              <h2 class="text-gray-900 text-lg font-semibold">WEEKLY-MONTHLY-CALENDAR</h2>
            </div>
            <div class="flex items-center gap-2 ml-6">
          <button 
                v-for="view in calendarViews" 
                :key="view.id"
                @click="selectedCalendarView = view.id"
                :class="[
                  'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                  selectedCalendarView === view.id 
                    ? 'bg-gray-900 text-white' 
                    : 'bg-gray-100 text-gray-600 hover:text-gray-900'
                ]"
              >
                {{ view.label }}
              </button>
            </div>
          </div>
          
          <div class="flex items-center gap-4">
            <!-- Action Icons -->
            <div class="flex items-center gap-2">
              <button class="text-gray-600 hover:text-gray-900 p-2 rounded transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                </svg>
              </button>
              <button class="text-gray-600 hover:text-gray-900 p-2 rounded transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                </svg>
              </button>
              <button class="text-gray-600 hover:text-gray-900 p-2 rounded transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </button>
              <button class="text-gray-600 hover:text-gray-900 p-2 rounded transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </button>
              <button class="text-gray-600 hover:text-gray-900 p-2 rounded transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </button>
            </div>
            
            <button class="bg-gray-100 hover:bg-gray-200 text-gray-900 px-4 py-2 rounded-lg font-medium transition-colors">
              Manage in Calendar
            </button>
            
            <!-- Navigation -->
            <div class="flex items-center gap-2">
              <button @click="previousMonth" class="text-gray-600 hover:text-gray-900 p-2 rounded transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </button>
              <button @click="goToToday" class="text-gray-600 hover:text-gray-900 px-4 py-2 rounded-lg font-medium transition-colors">
                Today
              </button>
              <button @click="nextMonth" class="text-gray-600 hover:text-gray-900 p-2 rounded transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </button>
            </div>
            
            <button class="bg-gray-900 text-white px-4 py-2 rounded-lg font-medium hover:bg-gray-800 transition-colors flex items-center gap-2">
              New
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
          </button>
          </div>
        </div>
        
        <!-- Calendar Display -->
        <div class="fbfm-calendar-display">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-900">{{ currentMonthYear }}</h3>
          </div>
          
          <!-- Days of Week Header -->
          <div class="grid grid-cols-7 gap-2 mb-2">
            <div 
              v-for="day in daysOfWeek" 
              :key="day"
              class="text-center text-sm font-medium text-gray-600 py-2"
            >
              {{ day }}
            </div>
          </div>
          
          <!-- Calendar Grid -->
          <div class="grid grid-cols-7 gap-2">
            <div 
              v-for="(date, index) in calendarDates" 
              :key="index"
              :class="[
                'relative p-3 rounded-lg text-center cursor-pointer transition-colors',
                date.isCurrentMonth ? 'text-gray-900 hover:bg-gray-50' : 'text-gray-400',
                date.isToday ? 'bg-pink-500 text-white font-semibold' : '',
                date.isSelected ? 'bg-pink-100' : ''
              ]"
              @click="selectDate(date)"
            >
              <div class="flex items-center justify-between">
                <span>{{ date.day }}</span>
                <button 
                  v-if="date.isCurrentMonth"
                  class="text-gray-400 hover:text-gray-600 text-xs"
                  @click.stop="addEvent(date)"
                >
                  +
                </button>
              </div>
            </div>
          </div>
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
import MarkdownEditor from './MilkDownEditor/MarkdownEditor.vue'

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
    project: 'Personal Goal',
    status: 'In Progress',
    timeTaken: '0h : 0m'
  },
  {
    id: 2,
    name: 'Hello world',
    project: '-',
    status: 'Pending',
    timeTaken: '0h : 0m'
  }
])

// Projects Data
const projects = ref([
  {
    id: 1,
    name: 'New Project',
    tasksToDo: 0,
    tasksDone: 0,
    totalTasks: 0,
    timeSpent: '0h : 0m'
  },
  {
    id: 2,
    name: 'Personal Goal',
    tasksToDo: 1,
    tasksDone: 0,
    totalTasks: 1,
    timeSpent: '0h : 0m'
  }
])

// Calendar
const calendarViews = [
  { id: 'timeline', label: 'Timeline' },
  { id: 'weekly', label: 'Weekly View' },
  { id: 'monthly', label: 'Monthly View' }
]

const selectedCalendarView = ref('monthly')
const currentDate = ref(dayjs())
const selectedDate = ref(null)

const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']

const currentMonthYear = computed(() => {
  return currentDate.value.format('MMMM YYYY')
})

const calendarDates = computed(() => {
  const startOfMonth = currentDate.value.startOf('month')
  const endOfMonth = currentDate.value.endOf('month')
  const startOfCalendar = startOfMonth.startOf('week')
  const endOfCalendar = endOfMonth.endOf('week')
  
  const dates = []
  let current = startOfCalendar
  
  while (current.isBefore(endOfCalendar) || current.isSame(endOfCalendar, 'day')) {
    const today = dayjs()
    dates.push({
      day: current.date(),
      date: current.format('YYYY-MM-DD'),
      isCurrentMonth: current.month() === currentDate.value.month(),
      isToday: current.isSame(today, 'day'),
      isSelected: selectedDate.value && current.isSame(selectedDate.value, 'day'),
      dateObj: current
    })
    current = current.add(1, 'day')
  }
  
  return dates
})

const selectDate = (date) => {
  selectedDate.value = date.dateObj
}

const addEvent = (date) => {
  console.log('Add event for:', date.date)
  // Handle add event logic
}

const previousMonth = () => {
  currentDate.value = currentDate.value.subtract(1, 'month')
}

const nextMonth = () => {
  currentDate.value = currentDate.value.add(1, 'month')
}

const goToToday = () => {
  currentDate.value = dayjs()
  selectedDate.value = dayjs()
}

// Notes
const noteContent = ref('')
const notes = ref([])

const saveNote = () => {
  if (!noteContent.value.trim()) {
    return
  }
  
  const newNote = {
    id: Date.now(),
    content: noteContent.value,
    createdAt: dayjs().toISOString(),
    updatedAt: dayjs().toISOString()
  }
  
  notes.value.unshift(newNote)
  noteContent.value = ''
  
  // Here you would typically save to backend via API
  console.log('Note saved:', newNote)
}

const handleNoteMediaUpload = (file) => {
  // Handle media upload for notes
  console.log('Media upload:', file)
  // You can implement file upload logic here
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
</style>

