/**
 * Mount NotesDrawerMount Vue component in board menu modal
 * This script mounts the NotesDrawerMount component when the Notes menu item is clicked
 */

import NotesDrawerMount from '../components/NotesDrawerMount.vue'

// Function to mount NotesDrawerMount component
window.mountNotesDrawer = function(containerId, boardId, retryCount = 0) {
    const maxRetries = 10
    const container = document.getElementById(containerId)
    
    if (!container) {
        console.error('Notes drawer container not found:', containerId)
        return
    }
    
    // Check if already mounted
    if (container.__vue_app__ || container.querySelector('.fbm-notes-mount')) {
        return
    }
    
    // Check if Vue and NotesDrawerMountComponent are available
    if (!window.Vue || !window.Vue.createApp) {
        if (retryCount < maxRetries) {
            container.innerHTML = '<div class="fbm-notes-loading" style="padding: 20px;">Loading Vue...</div>'
            setTimeout(() => {
                window.mountNotesDrawer(containerId, boardId, retryCount + 1)
            }, 200)
            return
        } else {
            container.innerHTML = '<div class="fbm-notes-error" style="padding: 20px; color: #ef4444;">Error: Vue is not available. Please refresh the page.</div>'
            console.error('Vue is not available after', maxRetries, 'retries')
            return
        }
    }
    
    // Get NotesDrawerMount component from window or use imported one
    const NotesDrawerMountComponent = window.NotesDrawerMountComponent || NotesDrawerMount
    
    if (!NotesDrawerMountComponent) {
        if (retryCount < maxRetries) {
            container.innerHTML = '<div class="fbm-notes-loading" style="padding: 20px;">Loading notes component...</div>'
            setTimeout(() => {
                window.mountNotesDrawer(containerId, boardId, retryCount + 1)
            }, 200)
            return
        } else {
            container.innerHTML = '<div class="fbm-notes-error" style="padding: 20px; color: #ef4444;">Error: Notes component is not available. Please refresh the page.</div>'
            console.error('NotesDrawerMountComponent is not available after', maxRetries, 'retries')
            return
        }
    }
    
    try {
        // Create Vue app with NotesDrawerMount component
        const notesApp = window.Vue.createApp(NotesDrawerMountComponent, {
            containerId: containerId,
            initialBoardId: boardId
        })
        
        // Try to use Element Plus if available
        if (window.ElementPlus) {
            notesApp.use(window.ElementPlus)
        }
        
        notesApp.mount(container)
        console.log('NotesDrawerMount mounted successfully for container:', containerId)
    } catch (error) {
        console.error('Error mounting NotesDrawerMount:', error)
        container.innerHTML = '<div class="fbm-notes-error" style="padding: 20px; color: #ef4444;">Error loading notes component: ' + error.message + '. Please refresh the page.</div>'
    }
}

// Auto-mount function that watches for containers
window.autoMountNotesDrawers = function() {
    const containers = document.querySelectorAll('[id^="fbm-notes-modal-"]')
    containers.forEach(container => {
        const containerId = container.id
        const boardId = container.getAttribute('data-board-id')
        
        if (!container.__vue_app__ && !container.querySelector('.fbm-notes-mount')) {
            // Wait a bit for the modal to be fully rendered
            setTimeout(() => {
                window.mountNotesDrawer(containerId, boardId)
            }, 200)
        }
    })
}

// Watch for new containers being added to the DOM
if (typeof MutationObserver !== 'undefined') {
    const observer = new MutationObserver(() => {
        window.autoMountNotesDrawers()
    })
    
    // Start observing when DOM is ready
    if (document.body) {
        observer.observe(document.body, {
            childList: true,
            subtree: true
        })
    }
    
    // Also run immediately
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', window.autoMountNotesDrawers)
    } else {
        window.autoMountNotesDrawers()
    }
}

