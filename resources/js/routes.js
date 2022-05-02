import Home from './components/Home.vue'
import Audiovisual from './components/Audiovisual.vue'

export default {
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'home',
            component: Home
        },
        {
            path: '/media/:id',
            name: 'audiovisual',
            component: Audiovisual
        }
    ]
}