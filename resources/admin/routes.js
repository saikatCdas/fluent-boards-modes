import FocusMode from './components/FocusMode.vue'
import ParaMode from './components/ParaMode.vue'

export default [
    {
        path: '/modes',
        redirect: '/modes/focus-modes'
    },
    {
        name: 'focus-modes',
        path: '/modes/focus-modes',
        component: FocusMode,
        meta: {
            active_menu: 'modes'
        }
    },
    {
        name: 'para-mode',
        path: '/modes/para',
        component: ParaMode,
        meta: {
            active_menu: 'modes'
        }
    }
]

