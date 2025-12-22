<template>
  <div class="para-container min-h-screen bg-white">
    <!-- Header -->
    <div class="para-header bg-white px-6 py-4 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">PARA Dashboard</h1>
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

    <div class="para-layout flex">
      <!-- Left Sidebar -->
      <div class="para-sidebar bg-white w-64 min-h-screen p-4">
        <!-- Quick-Action Section -->
        <div class="para-quick-action mb-6 border border-gray-200 p-8 rounded-lg">
          <h2 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <span class="w-8 h-8 rounded-full bg-white border-2 border-gray-900 flex items-center justify-center text-gray-900 text-lg font-bold">+</span>
            <span>Quick-Action</span>
          </h2>
          <div class="space-y-2">
            <button 
              v-for="action in quickActions" 
              :key="action.key"
              class="w-full text-left px-3 py-2 rounded-lg bg-gray-100 text-gray-900 flex items-center gap-2 transition-colors hover:bg-gray-200"
            >
              <span class="text-gray-900 font-bold">+</span>
              <span class="font-medium">{{ action.label }}</span>
            </button>
          </div>
        </div>

        <!-- Navigation Section -->
        <div class="para-navigation mb-6 border border-gray-200 p-8 rounded-lg">
          <h2 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
            <span>Navigation</span>
          </h2>
          <div class="space-y-2">
            <button 
              v-for="nav in navigationItems" 
              :key="nav.key"
              class="w-full text-left px-3 py-2 rounded-lg bg-gray-100 text-gray-900 flex items-center gap-2 transition-colors hover:bg-gray-200 underline"
            >
              <span v-if="!nav.isIcon" class="w-6 h-6 rounded-full bg-white border border-gray-300 flex items-center justify-center text-gray-900 text-xs font-semibold">
                {{ nav.icon }}
              </span>
              <svg v-else class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
              <span class="font-medium">{{ nav.label }}</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Main Content Area -->
      <div class="para-main-content flex-1 p-6">
        <!-- Folders Section -->
        <div v-if="viewConfig.folders" class="para-folders mb-8 bg-white border border-gray-200 rounded-lg p-6">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
              <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <h2 class="text-lg font-semibold text-gray-900">Folders</h2>
            </div>
            <div class="flex items-center gap-2">
              <div class="flex items-center gap-1">
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                  </svg>
                </button>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                  </svg>
                </button>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </button>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                  </svg>
                </button>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                  </svg>
                </button>
              </div>
              <button class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-1">
                <span>New</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
            </div>
          </div>
          <div class="text-sm text-gray-600 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span>Folder's</span>
          </div>

          <!-- Folders Grid -->
          <div class="grid grid-cols-3 gap-4">
            <div 
              v-for="folder in folders" 
              :key="folder.id"
              class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
            >
              <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 flex items-center justify-center">
                  <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="folder.iconPath" />
                  </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900">{{ folder.name }}</h3>
              </div>
              <p class="text-sm text-gray-600">Completed Boards : {{ folder.completed }}/{{ folder.total }}</p>
            </div>
            
            <!-- Empty Cards -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow flex flex-col items-center justify-center min-h-[120px] cursor-pointer">
              <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <span class="text-sm text-gray-500">New folder</span>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow flex flex-col items-center justify-center min-h-[120px] cursor-pointer">
              <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span class="text-sm text-gray-500">New folder</span>
            </div>
          </div>
        </div>

        <!-- Boards Section -->
        <div v-if="viewConfig.boards" class="para-boards mt-8 bg-white border border-gray-200 rounded-lg p-6">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
              <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <h2 class="text-lg font-semibold text-gray-900">Boards</h2>
            </div>
            <div class="flex items-center gap-2">
              <div class="flex items-center gap-1">
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                  </svg>
                </button>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                  </svg>
                </button>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </button>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                  </svg>
                </button>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                  </svg>
                </button>
              </div>
              <button class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-1">
                <span>New</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Board Tabs -->
          <div class="flex items-center gap-2 mb-4">
                <button 
              v-for="tab in boardTabs" 
              :key="tab.key"
                  :class="[
                'px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2',
                activeBoardTab === tab.key 
                  ? 'bg-gray-100 text-gray-900' 
                  : 'text-gray-600 hover:bg-gray-50'
              ]"
              @click="activeBoardTab = tab.key"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                  </svg>
                  <span>{{ tab.label }}</span>
                </button>
              </div>

          <!-- Boards Grid -->
          <div class="grid grid-cols-3 gap-4">
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow flex flex-col items-center justify-center min-h-[120px] cursor-pointer">
              <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span class="text-sm text-gray-500">New board</span>
            </div>
          </div>
        </div>

        <!-- Tasks Section -->
        <div v-if="viewConfig.tasks" class="para-tasks mt-8 bg-white border border-gray-200 rounded-lg p-6">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
              <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
              </svg>
              <h2 class="text-lg font-semibold text-gray-900">Tasks</h2>
            </div>
            <div class="flex items-center gap-2">
              <div class="flex items-center gap-1">
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                  </svg>
                </button>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                  </svg>
                </button>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                </button>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </button>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                  </svg>
                </button>
              </div>
              <div class="relative">
                <button 
                  @click="showNewDropdown = !showNewDropdown"
                  class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-1"
                >
                  <span>New</span>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>
                <div 
                  v-if="showNewDropdown"
                  class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10"
                >
                  <button class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center gap-2 text-gray-700">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>Tasks</span>
                  </button>
                  <button class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center gap-2 text-gray-700">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>All</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Task Filter Tabs -->
          <div class="flex items-center gap-2 mb-4">
            <button 
              v-for="tab in taskTabs" 
              :key="tab.key"
              :class="[
                'px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2',
                activeTaskTab === tab.key 
                  ? 'bg-gray-100 text-gray-900' 
                  : 'text-gray-600 hover:bg-gray-50'
              ]"
              @click="activeTaskTab = tab.key"
            >
              <svg v-if="tab.icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="tab.icon" />
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
              </svg>
              <span>{{ tab.label }}</span>
            </button>
            <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors text-gray-600">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </button>
          </div>

          <!-- Tasks Content -->
          <div class="relative">
            <button class="bg-white border border-gray-200 rounded-lg px-6 py-4 hover:shadow-md transition-shadow flex items-center gap-2 text-gray-700 w-full text-left">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span class="text-sm font-medium">New task</span>
            </button>
          </div>
        </div>

        <!-- Archive Section -->
        <div v-if="viewConfig.archive" class="para-archive mt-8 bg-white border border-gray-200 rounded-lg p-6">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
              <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
              </svg>
              <h2 class="text-lg font-semibold text-gray-900">Archive</h2>
            </div>
            <div class="flex items-center gap-2">
              <div class="flex items-center gap-1">
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                  </svg>
                </button>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                  </svg>
                </button>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                </button>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </button>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                  </svg>
                </button>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                  </svg>
                </button>
              </div>
              <div class="relative">
                <button 
                  @click="showArchiveNewDropdown = !showArchiveNewDropdown"
                  class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-1"
                >
                <span>New</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
                <div 
                  v-if="showArchiveNewDropdown"
                  class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10"
                >
                  <button class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center gap-2 text-gray-700">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    <span>Archive</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Archive Filter Tabs -->
          <div class="flex items-center gap-2 mb-4 border-b border-gray-200 pb-2">
            <button 
              v-for="tab in archiveTabs" 
              :key="tab.key"
              :class="[
                'px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2',
                activeArchiveTab === tab.key 
                  ? 'bg-gray-100 text-gray-900' 
                  : 'text-gray-600 hover:bg-gray-50'
              ]"
              @click="activeArchiveTab = tab.key"
            >
              <svg v-if="tab.icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="tab.icon" />
              </svg>
              <span>{{ tab.label }}</span>
            </button>
            <span class="text-sm text-gray-500 ml-2">2 more...</span>
          </div>

          <!-- Archive Content -->
          <div class="relative">
            <button class="bg-white border border-gray-200 rounded-lg px-6 py-4 hover:shadow-md transition-shadow flex items-center gap-2 text-gray-700 w-full text-left">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span class="text-sm font-medium">New board</span>
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- Tasks Timeline View -->
    <div v-if="viewConfig.tasksTimeline" class="para-tasks-timeline m-10 bg-white border border-gray-200 rounded-lg p-6">
      <!-- Header -->
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
          </svg>
          <h2 class="text-xl font-bold text-gray-900">Tasks</h2>
        </div>
        <div class="flex items-center gap-2">
          <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
          </button>
          <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
            </svg>
          </button>
          <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </button>
          <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>
          <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
            </svg>
          </button>
          <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
            </svg>
          </button>
          <div class="relative">
            <button @click="showTimelineNewDropdown = !showTimelineNewDropdown"
              class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-1">
              <span>New</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div v-if="showTimelineNewDropdown"
              class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
              <button class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center gap-2 text-gray-700">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>Tasks</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- View Selectors -->
      <div class="flex items-center gap-2 mb-4">
        <button class="p-1 hover:bg-gray-100 rounded transition-colors text-gray-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
        </button>
        <button class="p-1 hover:bg-gray-100 rounded transition-colors text-gray-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
          </svg>
        </button>
        <button :class="[
          'px-3 py-1.5 rounded-lg font-medium transition-colors flex items-center gap-2',
          activeTimelineView === 'thikweek'
            ? 'bg-gray-100 text-gray-900'
            : 'text-gray-600 hover:bg-gray-50'
        ]" @click="activeTimelineView = 'thikweek'">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <span>Thik Week</span>
        </button>
        <button :class="[
          'px-3 py-1.5 rounded-lg font-medium transition-colors flex items-center gap-2',
          activeTimelineView === 'timeline'
            ? 'bg-gray-100 text-gray-900'
            : 'text-gray-600 hover:bg-gray-50'
        ]" @click="activeTimelineView = 'timeline'">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Timeline</span>
        </button>
      </div>

      <!-- Date Navigation -->
      <div class="flex items-center justify-end gap-3 mb-4">
        <button
          class="px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <span>Manage in Calendar</span>
        </button>
        <div class="relative">
          <select
            class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg appearance-none pr-8">
            <option>Month</option>
          </select>
          <svg class="w-4 h-4 text-gray-600 absolute right-2 top-1/2 transform -translate-y-1/2 pointer-events-none"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </div>
        <div class="flex items-center gap-1">
          <button class="p-1.5 hover:bg-gray-100 rounded transition-colors text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <button class="px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
            Today
          </button>
          <button class="p-1.5 hover:bg-gray-100 rounded transition-colors text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Timeline Content -->
      <div class="para-timeline-content">
        <!-- Date Header -->
        <div class="flex items-center mb-2 border-b border-gray-200 pb-2">
          <div class="w-24 flex-shrink-0"></div>
          <div class="flex-1 flex items-center">
            <div class="flex items-center gap-1">
              <span class="text-sm font-semibold text-gray-700">August 2025</span>
              <div class="flex gap-1 ml-2">
                <span v-for="date in augustDates" :key="date" class="text-xs text-gray-600 w-12 text-center">{{ date
                  }}</span>
              </div>
            </div>
            <div class="flex items-center gap-1 ml-4">
              <span class="text-sm font-semibold text-gray-700">September</span>
              <div class="flex gap-1 ml-2">
                <span v-for="date in septemberDates" :key="date" class="text-xs text-gray-600 w-12 text-center">{{ date
                  }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Timeline Grid -->
        <div class="flex items-start">
          <!-- Left Sidebar with New Button -->
          <div class="w-24 flex-shrink-0 pt-2">
            <button
              class="w-full px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors flex items-center gap-2 border border-gray-200">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span>New</span>
            </button>
          </div>

          <!-- Timeline Columns -->
          <div class="flex-1 flex border-l border-gray-200">
            <!-- August Dates -->
            <div v-for="date in augustDates" :key="'aug-' + date"
              class="flex-1 border-r border-gray-200 min-h-[400px] bg-gray-50"></div>

            <!-- Week Separator (thicker line) -->
            <div class="w-0.5 bg-gray-300"></div>

            <!-- September Dates -->
            <div v-for="date in septemberDates" :key="'sep-' + date"
              class="flex-1 border-r border-gray-200 min-h-[400px] bg-gray-50"
              :class="{ 'border-r-2 border-gray-300': date === 7 || date === 14 }"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Configuration Dialog -->
    <div v-if="showConfigDialog" class="para-config-overlay fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showConfigDialog = false">
      <div class="para-config-dialog bg-white rounded-xl shadow-2xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
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
            Select which sections you want to display in PARA Dashboard:
          </div>
          
          <div class="space-y-3">
            <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
              <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <div>
                  <div class="font-medium text-gray-900">Folders</div>
                  <div class="text-sm text-gray-500">View and manage your folders</div>
                </div>
              </div>
              <input 
                type="checkbox" 
                v-model="viewConfig.folders"
                class="w-5 h-5 text-gray-800 rounded focus:ring-gray-800"
              />
            </label>
            
            <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
              <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
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
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
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
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                <div>
                  <div class="font-medium text-gray-900">Archive</div>
                  <div class="text-sm text-gray-500">View archived items</div>
                </div>
              </div>
              <input 
                type="checkbox" 
                v-model="viewConfig.archive"
                class="w-5 h-5 text-gray-800 rounded focus:ring-gray-800"
              />
            </label>
            
            <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
              <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <div>
                  <div class="font-medium text-gray-900">Tasks Timeline</div>
                  <div class="text-sm text-gray-500">Timeline view of your tasks</div>
                </div>
              </div>
              <input 
                type="checkbox" 
                v-model="viewConfig.tasksTimeline"
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

<script>
export default {
  name: 'ParaMode',
  data() {
    return {
      activeBoardTab: 'active',
            activeTaskTab: 'next7days',
            activeArchiveTab: 'boards',
            activeTimelineView: 'timeline',
            showNewDropdown: false,
            showArchiveNewDropdown: false,
            showTimelineNewDropdown: false,
            showConfigDialog: false,
            defaultViewConfig: {
              folders: true,
              boards: true,
              tasks: true,
              archive: true,
              tasksTimeline: true
            },
            viewConfig: {
              folders: true,
              boards: true,
              tasks: true,
              archive: true,
              tasksTimeline: true
            },
            augustDates: [28, 29, 30, 31],
            septemberDates: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23],
      quickActions: [
                { key: 'folder', label: 'Folder' },
                { key: 'task', label: 'Task' },
                { key: 'board', label: 'Board' },
                { key: 'note', label: 'Note' },
      ],
      navigationItems: [
                { key: 'boards', label: 'Boards', icon: 'B' },
                { key: 'folders', label: 'Folders', icon: 'F' },
                { key: 'archives', label: 'Archives', icon: 'A' },
                { key: 'tasks', label: 'Tasks', icon: 'T', isIcon: true }
      ],
      folders: [
        {
                    id: 1,
          name: 'Finances',
          completed: 0,
                    total: 0,
                    iconPath: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
        },
        {
                    id: 2,
          name: 'Health',
          completed: 0,
                    total: 0,
                    iconPath: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'
        },
        {
                    id: 3,
          name: 'Skill',
          completed: 0,
                    total: 0,
                    iconPath: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'
        },
        {
                    id: 4,
          name: 'Personal',
          completed: 0,
                    total: 0,
                    iconPath: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'
        }
      ],
      boardTabs: [
                { key: 'active', label: 'Active' },
                { key: 'completed', label: 'Completed' },
                { key: 'all', label: 'All' }
            ],
            taskTabs: [
                { key: 'next7days', label: 'Next 7 Days', icon: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z' },
                { key: 'overdue', label: 'Overdue', icon: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
                { key: 'all', label: 'All' }
            ],
            archiveTabs: [
                { key: 'boards', label: 'Boards', icon: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4' },
                { key: 'folders', label: 'Folders', icon: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z' },
                { key: 'tasks', label: 'Tasks', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' }
            ]
        }
    },
    mounted() {
        // Close dropdown when clicking outside
        document.addEventListener('click', this.handleClickOutside);
        // Load view configuration
        this.loadViewConfig();
    },
    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
    },
    methods: {
        handleClickOutside(event) {
            if (!this.$el.contains(event.target)) {
                this.showNewDropdown = false;
                this.showArchiveNewDropdown = false;
                this.showTimelineNewDropdown = false;
            }
        },
        loadViewConfig() {
            try {
                const saved = localStorage.getItem('para_view_config');
                if (saved) {
                    const parsed = JSON.parse(saved);
                    this.viewConfig = { ...this.defaultViewConfig, ...parsed };
                }
            } catch (error) {
                console.error('Failed to load view config:', error);
            }
        },
        saveViewConfig() {
            try {
                localStorage.setItem('para_view_config', JSON.stringify(this.viewConfig));
                this.showConfigDialog = false;
            } catch (error) {
                console.error('Failed to save view config:', error);
            }
        },
        resetViewConfig() {
            this.viewConfig = { ...this.defaultViewConfig };
            try {
                localStorage.removeItem('para_view_config');
            } catch (error) {
                console.error('Failed to reset view config:', error);
            }
    }
  }
}
</script>

<style scoped>
.para-container {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.para-sidebar {
    background: #ffffff;
}

.para-main-content {
    background: #ffffff;
}

.para-tasks-timeline {
    background: #ffffff;
}

.para-timeline-content {
    overflow-x: auto;
}

.para-timeline-content .flex-1 {
    min-width: 48px;
}

.para-config-overlay {
    z-index: 9999;
}

.para-config-dialog {
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
</style>

