import { createApp } from 'vue'
import { createRouter, createWebHashHistory } from 'vue-router'
import App from './App.vue'
import routes from '../routes.js'
import '../styles/app.css'
import 'element-plus/dist/index.css'
import { ElPopover } from 'element-plus'

let isMounted = false
let mountAttempts = 0
const maxAttempts = 50 // Stop after 5 seconds (50 * 100ms)

// Wait for fluent-boards app to be ready
function initModesMenu() {
    // Don't mount twice
    if (isMounted) {
        return
    }

    mountAttempts++

    const flApp = document.querySelector('.fl_app')
    
    if (!flApp) {
        // Retry after a short delay if fl_app is not found
        if (mountAttempts < maxAttempts) {
            setTimeout(initModesMenu, 100)
        }
        return
    }

    // Check if we've already mounted
    if (flApp.querySelector('#fbm-modes-menu')) {
        isMounted = true
        return
    }

    // Wait a bit more to ensure fluent-boards Vue app is initialized
    setTimeout(() => {
        const flAppElement = document.querySelector('.fl_app')
        
        if (!flAppElement) {
            return
        }

        // Double check we haven't mounted
        if (flAppElement.querySelector('#fbm-modes-menu')) {
            isMounted = true
            return
        }

        // Create a container for our menu
        const menuContainer = document.createElement('div')
        menuContainer.id = 'fbm-modes-menu'
        menuContainer.className = 'fbm-modes-menu-container'
        
        // Insert at the beginning of fl_app
        flAppElement.insertBefore(menuContainer, flAppElement.firstChild)

        // Create and mount Vue app
        try {
            // Create router
            const router = createRouter({
                routes,
                history: createWebHashHistory()
            })

            const app = createApp(App)
            app.use(router)
            app.component('ElPopover', ElPopover)
            app.mount('#fbm-modes-menu')
            isMounted = true
            console.log('Fluent Boards Modes app mounted successfully')
        } catch (error) {
            console.error('Error mounting Fluent Boards Modes app:', error)
            // Remove the container if mounting failed
            const container = document.querySelector('#fbm-modes-menu')
            if (container) {
                container.remove()
            }
        }
    }, 300)
}

// Wait for DOM to be ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initModesMenu)
} else {
    initModesMenu()
}

// Also listen for fluent-boards app ready event if it exists
document.addEventListener('fluentComPortalAppReady', () => {
    setTimeout(initModesMenu, 200)
})

// Use MutationObserver to watch for .fl_app to appear
if (document.body) {
    const observer = new MutationObserver((mutations, obs) => {
        const flApp = document.querySelector('.fl_app')
        if (flApp && !isMounted && mountAttempts < maxAttempts) {
            initModesMenu()
            // Disconnect after finding fl_app to avoid unnecessary checks
            if (isMounted) {
                obs.disconnect()
            }
        }
    })

    // Start observing
    observer.observe(document.body, {
        childList: true,
        subtree: true
    })

    // Disconnect observer after max attempts
    setTimeout(() => {
        observer.disconnect()
    }, maxAttempts * 100)
}
