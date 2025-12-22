<template>
  <div class="fbm-notes-mount">
    <div v-if="loading" class="fbm-notes-loading" style="padding: 20px;">
      {{ loadingMessage }}
    </div>
    <div v-else-if="error" class="fbm-notes-error" style="padding: 20px; color: #ef4444;">
      {{ error }}
    </div>
    <NotesDrawer v-else-if="boardId" :board-id="boardId" />
  </div>
</template>

<script>
import NotesDrawer from './NotesDrawer.vue'

export default {
  name: 'NotesDrawerMount',
  components: {
    NotesDrawer
  },
  props: {
    containerId: {
      type: String,
      required: true
    },
    initialBoardId: {
      type: [String, Number],
      default: null
    }
  },
  data() {
    return {
      boardId: null,
      loading: true,
      loadingMessage: 'Detecting board ID...',
      error: null,
      retryCount: 0,
      maxRetries: 10
    }
  },
  mounted() {
    this.detectAndMount()
  },
  methods: {
    detectAndMount() {
      // Try to get board ID from various sources
      let detectedBoardId = this.initialBoardId
      
      // If initialBoardId is missing or invalid, try to get it from context
      if (!detectedBoardId || detectedBoardId === 'null' || detectedBoardId === '{{BOARD_ID}}') {
        detectedBoardId = this.getBoardIdFromContext()
      }
      
      if (!detectedBoardId) {
        if (this.retryCount < this.maxRetries) {
          this.loadingMessage = 'Detecting board ID...'
          this.retryCount++
          setTimeout(() => {
            this.detectAndMount()
          }, 200)
          return
        } else {
          this.error = 'Board ID not found. Please ensure you are viewing a board.'
          this.loading = false
          console.error('Board ID not found after', this.maxRetries, 'retries')
          return
        }
      }
      
      this.boardId = detectedBoardId
      this.loading = false
    },
    
    getBoardIdFromContext() {
      // Try to get from container data attribute first
      const container = document.getElementById(this.containerId)
      if (container) {
        let boardId = container.getAttribute('data-board-id')
        if (boardId && boardId !== '{{BOARD_ID}}' && boardId !== '') {
          return boardId
        }
      }
      
      // Try to get from fluent-boards global vars
      if (typeof window.fluentAddonVars !== 'undefined' && window.fluentAddonVars.board_id) {
        return window.fluentAddonVars.board_id
      }
      
      // Try to get from URL hash (e.g., #/boards/123)
      const hashMatch = window.location.hash.match(/\/boards?\/(\d+)/)
      if (hashMatch && hashMatch[1]) {
        return hashMatch[1]
      }
      
      // Try to get from URL path
      const pathMatch = window.location.pathname.match(/\/board\/(\d+)/)
      if (pathMatch && pathMatch[1]) {
        return pathMatch[1]
      }
      
      return null
    }
  }
}
</script>

<style scoped>
.fbm-notes-mount {
  width: 100%;
}
</style>

