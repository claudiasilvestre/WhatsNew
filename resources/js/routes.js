import Home from './components/home/Home.vue'
import Audiovisual from './components/audiovisual/Audiovisual.vue'
import Capitulo from './components/capitulo/Capitulo.vue'
import Participante from './components/Participante.vue'

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
        },
        {
            path: '/media/:idAudiovisual/episode/:idCapitulo',
            name: 'capitulo',
            component: Capitulo
        },
        {
            path: '/media/:idAudiovisual/information/:idPersona',
            name: 'informacion',
            component: Participante
        }
    ]
}