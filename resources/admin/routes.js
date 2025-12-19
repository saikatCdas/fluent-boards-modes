import FocusMode from './components/FocusMode.vue'

export default [
    {
        path: '/',
        redirect: '/modes'
    },
    {
        name: 'focus-modes',
        path: '/modes/focus-modes',
        component: FocusMode,
        meta: {
            active_menu: 'modes'
        }
    }
]

