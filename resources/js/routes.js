import Home from './components/home/Home.vue'
import Audiovisual from './components/audiovisual/Audiovisual.vue'

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