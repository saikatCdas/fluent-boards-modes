<template>
  <div class="fbm-notes-drawer">
    <div class="fbm-notes-modal">
      <div class="fbm-notes-content">
        <!-- Search and Add Note Bar -->
        <div v-if="!showCreateForm && !currentNote" class="fbm-notes-toolbar">
          <el-input
            v-model="searchQuery"
            placeholder="Search notes..."
            class="fbm-search-input"
            clearable
          >
            <template #prefix>
              <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
              </svg>
            </template>
          </el-input>
          <el-button
            type="primary"
            @click="showCreateForm = true"
            class="fbm-add-note-btn"
          >
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="margin-right: 6px;">
              <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Add Note
          </el-button>
        </div>
        <!-- Create Note Form (shown when showCreateForm is true) -->
        <div v-if="showCreateForm" class="fbm-create-note-section">
          <div class="fbm-notes-editor">
            <input
              v-model="noteTitle"
              type="text"
              placeholder="Note title"
              class="fbm-note-title-input"
              :key="`note-title-${editorKey}`"
            />
            <div class="fbm-markdown-editor-wrapper">
              <MarkdownEditor
                :key="`note-editor-${editorKey}`"
                v-model="noteContent"
                placeholder="Type your note description here..."
                :autofocus="false"
                :disable_mention="true"
                :disable_media="false"
                @handleMediaUpload="handleNoteMediaUpload"
              />
            </div>
            <div class="fbm-note-actions">
              <button 
                @click="cancelCreate"
                class="fbm-cancel-note-btn"
              >
                Cancel
              </button>
              <button 
                @click="saveNote"
                :disabled="!noteContent.trim() || saving"
                class="fbm-save-note-btn"
              >
                Save Note
              </button>
            </div>
          </div>
          <div
            v-if="resultMessage"
            :class="['fbm-note-result', resultType]"
          >
            {{ resultMessage }}
          </div>
        </div>

        <!-- Notes List (shown when showCreateForm is false and no note is selected) -->
        <div v-else-if="!currentNote" class="fbm-notes-list-container">
          <div v-loading="loading" class="fbm-notes-list">
            <div v-if="filteredNotes.length === 0 && !loading" class="fbm-notes-empty">
              <svg width="80" height="80" viewBox="0 0 16 16" fill="currentColor" class="fbm-notes-empty-icon">
                <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                <path d="M5 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
              </svg>
              <p class="fbm-notes-empty-text" v-if="searchQuery">No notes found matching your search.</p>
              <p class="fbm-notes-empty-text" v-else>No notes yet.</p>
              <p class="fbm-notes-empty-instruction" v-if="!searchQuery">Click 'Add Note' to create your first note.</p>
            </div>
            <div v-else class="fbm-notes-items">
              <div
                v-for="note in filteredNotes"
                :key="note.id"
                class="fbm-note-item"
                @click="openNoteView(note)"
              >
                <div class="fbm-note-header">
                  <div class="fbm-note-info">
                    <h4 v-if="note.title" class="fbm-note-title">{{ note.title }}</h4>
                  </div>
                </div>
                <div class="fbm-note-content feed_texts feed_md_content" v-html="parseMessage(note.content)"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Note View/Edit (shown when a note is selected) -->
        <div v-else class="fbm-note-view-container">
          <!-- View Mode -->
          <div v-if="!isEditMode" class="fbm-note-view-mode">
            <div class="fbm-note-view-header">
              <button
                @click="closeNoteView"
                class="fbm-back-btn"
                title="Back to notes"
              >
                <svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor">
                  <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
              </button>
              <h2 v-if="currentNote.title" class="fbm-note-view-title">{{ currentNote.title }}</h2>
              <div class="fbm-note-view-actions">
                <button
                  @click="isEditMode = true"
                  class="fbm-edit-note-btn"
                  title="Edit note"
                >
                  <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.179z"/>
                  </svg>
                </button>
                <button
                  @click="deleteNoteFromView(currentNote.id)"
                  class="fbm-delete-note-btn"
                  title="Delete note"
                >
                  <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M5.5 5.5A.5.5 0 016 6v6a.5.5 0 01-1 0V6a.5.5 0 01.5-.5zm2.5 0a.5.5 0 01.5.5v6a.5.5 0 01-1 0V6a.5.5 0 01.5-.5zm3 .5a.5.5 0 00-1 0v6a.5.5 0 001 0V6z"/>
                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 01-1 1H13v9a2 2 0 01-2 2H5a2 2 0 01-2-2V4h-.5a1 1 0 01-1-1V2a1 1 0 011-1H6a1 1 0 011-1h2a1 1 0 011 1h3.5a1 1 0 011 1v1zM4.118 4L4 4.059V13a1 1 0 001 1h6a1 1 0 001-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                  </svg>
                </button>
              </div>
            </div>
            <div class="fbm-note-view-body feed_texts feed_md_content" v-html="parseMessage(currentNote.content)"></div>
          </div>

          <!-- Edit Mode -->
          <div v-else class="fbm-note-edit-mode">
            <div class="fbm-note-edit-form">
              <input
                v-model="editNoteTitle"
                type="text"
                placeholder="Note title"
                class="fbm-note-title-input"
                :key="`edit-note-title-${editorKey}`"
              />
              <div class="fbm-markdown-editor-wrapper">
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
              <div class="fbm-note-edit-actions">
                <button @click="cancelEdit" class="fbm-cancel-note-btn">Cancel</button>
                <button 
                  @click="updateNote"
                  :disabled="!editNoteContent.trim() || saving"
                  class="fbm-save-note-btn"
                >
                  Save Changes
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Rest from '../Bits/Rest.js'
import MarkdownEditor from './MilkDownEditor/MarkdownEditor.vue'

export default {
  name: 'NotesDrawer',
  components: {
    MarkdownEditor
  },
  props: {
    boardId: {
      type: [Number, String],
      required: true
    }
  },
  data() {
    return {
      notes: [],
      noteTitle: '',
      noteContent: '',
      loading: false,
      saving: false,
      resultMessage: '',
      resultType: 'success',
      showCreateForm: false,
      searchQuery: '',
      editorKey: 0,
      currentNote: null,
      isEditMode: false,
      editNoteTitle: '',
      editNoteContent: ''
    }
  },
  computed: {
    filteredNotes() {
      if (!this.searchQuery.trim()) {
        return this.notes
      }
      
      const query = this.searchQuery.toLowerCase().trim()
      return this.notes.filter(note => {
        const title = (note.title || '').toLowerCase()
        return title.includes(query)
      })
    }
  },
  mounted() {
    this.loadNotes()
  },
  beforeUnmount() {
    // Reset state when component is about to be unmounted
    this.reset()
  },
  methods: {
    async loadNotes() {
      this.loading = true
      try {
        const response = await Rest.get('notes', {
          board_id: this.boardId
        })
        
        if (response.data && Array.isArray(response.data)) {
          this.notes = response.data.map(note => ({
            id: note.id,
            title: note.title || '',
            content: note.content || note.description || '',
            created_at: note.created_at || '',
            updated_at: note.updated_at || '',
            board_id: note.board_id
          }))
        } else {
          this.notes = []
        }
      } catch (error) {
        console.error('Error loading notes:', error)
        this.$notify.error({
          title: 'Error',
          message: 'Failed to load notes'
        })
        this.notes = []
      } finally {
        this.loading = false
      }
    },
    
    async saveNote() {
      if (!this.noteContent.trim()) {
        this.showResult('Note content is required', 'error')
        return
      }

      this.saving = true
      try {
        const response = await Rest.post('notes', {
          board_id: parseInt(this.boardId),
          title: this.noteTitle.trim() || null,
          content: this.noteContent.trim()
        })
        
        if (response.data) {
          this.showResult('Note saved successfully', 'success')
          // Clear editor and title after successful save
          this.noteTitle = ''
          this.noteContent = ''
          this.showCreateForm = false
          // Force editor reset by incrementing key
          this.editorKey++
          await this.loadNotes()
        } else {
          this.showResult(response.message || 'Failed to save note', 'error')
        }
      } catch (error) {
        console.error('Error saving note:', error)
        this.showResult('Failed to save note. Please try again.', 'error')
      } finally {
        this.saving = false
      }
    },
    
    async deleteNote(noteId) {
      try {
        await this.$confirm(
          'Are you sure you want to delete this note?',
          'Delete Note',
          {
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            type: 'warning'
          }
        )
        
        try {
          await Rest.delete(`notes/${noteId}`)
          this.$notify.success({
            title: 'Success',
            message: 'Note deleted successfully'
          })
          await this.loadNotes()
        } catch (error) {
          console.error('Error deleting note:', error)
          this.$notify.error({
            title: 'Error',
            message: 'Failed to delete note'
          })
        }
      } catch {
        // User cancelled
      }
    },
    
    showResult(message, type) {
      this.resultMessage = message
      this.resultType = type
      setTimeout(() => {
        this.resultMessage = ''
      }, 3000)
    },
    
    formatDate(dateString) {
      if (!dateString) return ''
      
      const date = new Date(dateString)
      const now = new Date()
      const diff = now - date
      const seconds = Math.floor(diff / 1000)
      const minutes = Math.floor(seconds / 60)
      const hours = Math.floor(minutes / 60)
      const days = Math.floor(hours / 24)

      if (seconds < 60) return 'Just now'
      if (minutes < 60) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`
      if (hours < 24) return `${hours} hour${hours > 1 ? 's' : ''} ago`
      if (days < 7) return `${days} day${days > 1 ? 's' : ''} ago`

      return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
    },
    
    parseMessage(message) {
      if (!message) {
        return ''
      }

      // First convert markdown to HTML
      let html = this.mdToHtml(message)

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
          if (hashtag && !this.isInsideHtmlTag(string, offset)) {
            return `<a href="#" onclick="return false;">#${hashtag}</a>`
          }

          // Case 3: It's any other HTML tag or a hashtag inside a tag
          return match
        }
      )

      return newMessage
    },
    
    mdToHtml(text) {
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
        return '<h4>' + this.processInlineMarkdown(content) + '</h4>'
      })
      text = text.replace(/^###\s+(.*?)$/gm, (match, content) => {
        return '<h3>' + this.processInlineMarkdown(content) + '</h3>'
      })
      text = text.replace(/^##\s+(.*?)$/gm, (match, content) => {
        return '<h2>' + this.processInlineMarkdown(content) + '</h2>'
      })
      text = text.replace(/^#\s+(.*?)$/gm, (match, content) => {
        return '<h1>' + this.processInlineMarkdown(content) + '</h1>'
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
    },
    
    processInlineMarkdown(content) {
      // Process inline markdown elements (links, bold, italic) in headings
      let processed = content
      // Links first
      processed = processed.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2">$1</a>')
      // Then bold
      processed = processed.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
      // Then italic
      processed = processed.replace(/\*(.*?)\*/g, '<em>$1</em>')
      return processed
    },
    
    isInsideHtmlTag(string, position) {
      let isInTag = false
      for (let i = 0; i < position; i++) {
        if (string[i] === '<') isInTag = true
        if (string[i] === '>') isInTag = false
      }
      return isInTag
    },
    
    cancelCreate() {
      this.showCreateForm = false
      this.noteTitle = ''
      this.noteContent = ''
      this.resultMessage = ''
      // Force editor reset by incrementing key
      this.editorKey++
    },
    
    handleNoteMediaUpload(file) {
      // Handle media upload for notes
      console.log('Media upload:', file)
      // You can implement file upload logic here
    },
    
    openNoteView(note) {
      this.currentNote = { ...note }
      this.editNoteTitle = note.title || ''
      this.editNoteContent = note.content || ''
      this.isEditMode = false
    },
    
    reset() {
      // Reset all component state to initial values
      this.showCreateForm = false
      this.currentNote = null
      this.isEditMode = false
      this.noteTitle = ''
      this.noteContent = ''
      this.editNoteTitle = ''
      this.editNoteContent = ''
      this.searchQuery = ''
      this.resultMessage = ''
      this.resultType = 'success'
      this.saving = false
      this.editorKey = 0
    },
    
    closeNoteView() {
      this.isEditMode = false
      this.currentNote = null
      this.editNoteTitle = ''
      this.editNoteContent = ''
    },
    
    cancelEdit() {
      this.isEditMode = false
      if (this.currentNote) {
        this.editNoteTitle = this.currentNote.title || ''
        this.editNoteContent = this.currentNote.content || ''
      }
    },
    
    async updateNote() {
      if (!this.editNoteContent.trim()) {
        this.$notify.warning({
          title: 'Warning',
          message: 'Note content is required'
        })
        return
      }

      if (!this.currentNote) {
        return
      }

      this.saving = true
      try {
        const response = await Rest.patch(`notes/${this.currentNote.id}`, {
          title: this.editNoteTitle.trim() || null,
          content: this.editNoteContent.trim()
        })
        
        if (response.data) {
          this.$notify.success({
            title: 'Success',
            message: 'Note updated successfully'
          })
          
          // Update the note in the list
          const index = this.notes.findIndex(n => n.id === this.currentNote.id)
          if (index !== -1) {
            this.notes[index] = {
              ...this.notes[index],
              title: response.data.title || '',
              content: response.data.content || response.data.description || '',
              updated_at: response.data.updated_at || ''
            }
          }
          
          // Update current note
          this.currentNote = {
            ...this.currentNote,
            title: response.data.title || '',
            content: response.data.content || response.data.description || '',
            updated_at: response.data.updated_at || ''
          }
          
          // Update edit fields
          this.editNoteTitle = response.data.title || ''
          this.editNoteContent = response.data.content || response.data.description || ''
          
          this.isEditMode = false
          this.editorKey++
        } else {
          this.$notify.error({
            title: 'Error',
            message: response.message || 'Failed to update note'
          })
        }
      } catch (error) {
        console.error('Error updating note:', error)
        this.$notify.error({
          title: 'Error',
          message: 'Failed to update note. Please try again.'
        })
      } finally {
        this.saving = false
      }
    },
    
    async deleteNoteFromView(noteId) {
      await this.deleteNote(noteId)
      if (this.currentNote) {
        this.closeNoteView()
      }
    }
  }
}
</script>

<style>
.fbm-notes-drawer {
  padding: 0;
  width: 100%;
  height: 100%;
}

.fbm-notes-modal {
  display: flex;
  flex-direction: column;
  height: 100%;
  background: #ffffff;
}

.fbm-notes-content {
  flex: 1;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
}

.fbm-notes-toolbar {
  display: flex;
  gap: 12px;
  margin-bottom: 24px;
  align-items: center;
}

.fbm-search-input {
  flex: 1;
}

.fbm-add-note-btn {
  background: #1f2937;
  border-color: #1f2937;
  display: flex;
  align-items: center;
  padding: 8px 16px;
  font-weight: 500;
  white-space: nowrap;
  color: #ffffff;
}

.fbm-add-note-btn:hover {
  background: #111827;
  border-color: #111827;
}

.fbm-notes-list-container {
  width: 100%;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.fbm-notes-list {
  min-height: 200px;
}

.fbm-notes-empty {
  text-align: center;
  padding: 80px 40px;
  color: #6b7280;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.fbm-notes-empty-icon {
  opacity: 0.3;
  margin-bottom: 24px;
  color: #9ca3af;
}

.fbm-notes-empty-text {
  margin: 0 0 8px 0;
  font-size: 16px;
  color: #374151;
  font-weight: 500;
}

.fbm-notes-empty-instruction {
  margin: 0;
  font-size: 14px;
  color: #9ca3af;
}

.fbm-notes-items {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.fbm-note-item {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 16px;
  transition: box-shadow 0.2s;
  cursor: pointer;
}

.fbm-note-item:hover {
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.fbm-note-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 12px;
}

.fbm-note-info {
  flex: 1;
}

.fbm-note-title {
  margin: 0 0 8px 0;
  font-size: 16px;
  font-weight: 600;
  color: #111827;
}

.fbm-note-date {
  color: #6b7280;
  font-size: 12px;
}

.fbm-delete-note-btn {
  background: transparent;
  border: none;
  color: #ef4444;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 4px;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.fbm-delete-note-btn:hover {
  background: #fee2e2;
}

.fbm-note-content {
  color: #374151;
  font-size: 14px;
  line-height: 1.6;
}

.fbm-note-content.feed_texts.feed_md_content {
  word-wrap: break-word;
}

.fbm-note-content.feed_texts.feed_md_content p {
  margin: 0 0 8px 0;
}

.fbm-note-content.feed_texts.feed_md_content p:last-child {
  margin-bottom: 0;
}

.fbm-note-content.feed_texts.feed_md_content a {
  color: #2563eb;
  text-decoration: none;
}

.fbm-note-content.feed_texts.feed_md_content a:hover {
  text-decoration: underline;
}

.fbm-note-content.feed_texts.feed_md_content strong {
  font-weight: 600;
}

.fbm-note-content.feed_texts.feed_md_content em {
  font-style: italic;
}

.fbm-note-content.feed_texts.feed_md_content code {
  background: #f3f4f6;
  padding: 2px 4px;
  border-radius: 3px;
  font-family: monospace;
  font-size: 0.9em;
}

.fbm-note-content.feed_texts.feed_md_content pre {
  background: #f3f4f6;
  padding: 12px;
  border-radius: 6px;
  overflow-x: auto;
  margin: 8px 0;
}

.fbm-note-content.feed_texts.feed_md_content ul,
.fbm-note-content.feed_texts.feed_md_content ol {
  margin: 8px 0;
  padding-left: 24px;
}

.fbm-note-content.feed_texts.feed_md_content li {
  margin: 4px 0;
}

.fbm-create-note-section {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.fbm-notes-editor {
  width: 100%;
}

.fbm-note-title-input {
  width: 100%;
  background: transparent;
  font-size: 18px;
  font-weight: bold;
  outline: none !important;
  box-shadow: none !important;
  border: none !important;
}

.fbm-note-title-input:focus {
  border-bottom-color: #1f2937;
}

.fbm-note-title-input::placeholder {
  color: #9ca3af;
}

.fbm-markdown-editor-wrapper {
  min-height: 300px;
  margin-bottom: 16px;
  padding: 0 8px;
}

.fbm-note-actions {
  margin-top: 16px;
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}

.fbm-cancel-note-btn {
  background: transparent;
  color: #6b7280;
  padding: 8px 24px;
  border-radius: 8px;
  font-weight: 500;
  border: 1px solid #e5e7eb;
  cursor: pointer;
  transition: all 0.2s;
}

.fbm-cancel-note-btn:hover {
  color: #374151;
  border-color: #d1d5db;
  background: #f9fafb;
}

.fbm-save-note-btn {
  background: #1f2937;
  color: #ffffff;
  padding: 8px 24px;
  border-radius: 8px;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: background-color 0.2s;
}

.fbm-save-note-btn:hover:not(:disabled) {
  background: #111827;
}

.fbm-save-note-btn:disabled {
  background: #d1d5db;
  cursor: not-allowed;
}

.fbm-cancel-btn {
  color: #6b7280;
}

.fbm-cancel-btn:hover {
  color: #374151;
}

.fbm-cancel-form-btn {
  color: #6b7280;
}

.fbm-cancel-form-btn:hover {
  color: #374151;
}

.fbm-note-result {
  margin-top: 12px;
  padding: 12px;
  border-radius: 6px;
  font-size: 14px;
}

.fbm-note-result.success {
  background: #d1fae5;
  color: #065f46;
  border: 1px solid #6ee7b7;
}

.fbm-note-result.error {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fca5a5;
}

/* Note View/Edit Styles */
.fbm-note-view-container {
  width: 100%;
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.fbm-note-view-mode {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.fbm-note-view-header {
  display: flex;
  align-items: start;
  gap: 12px;
  margin-bottom: 24px;
}

.fbm-back-btn {
  background: transparent;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 8px;
  border-radius: 4px;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.fbm-back-btn:hover {
  background: #f3f4f6;
  color: #374151;
}

.fbm-note-view-title {
  margin: 0;
  font-size: 24px;
  font-weight: 600;
  color: #111827;
  flex: 1;
}

.fbm-note-view-actions {
  display: flex;
  gap: 8px;
  align-items: center;
  flex-shrink: 0;
}

.fbm-edit-note-btn {
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

.fbm-edit-note-btn:hover {
  background: #eff6ff;
}

.fbm-note-view-body {
  color: #374151;
  font-size: 14px;
  line-height: 1.6;
  min-height: 100px;
}

.fbm-note-edit-mode {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.fbm-note-edit-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.fbm-note-edit-actions {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  margin-top: 16px;
}
</style>

