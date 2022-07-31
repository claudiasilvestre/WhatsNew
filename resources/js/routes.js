import VueRouter from 'vue-router'
// import store from './store'
import Home from './components/home/Home.vue'
import Audiovisual from './components/audiovisual/Audiovisual.vue'
import Capitulo from './components/capitulo/Capitulo.vue'
import Participante from './components/Participante.vue'
import Perfil from './components/perfil/Perfil.vue'
import MenuAjustes from './components/perfil/MenuAjustes.vue'
import Search from './components/Search.vue'
import MiColeccion from './components/coleccion/MiColeccion.vue'
import Index from './components/Index.vue'

const router = new VueRouter ({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'index',
            component: Index,
        },
        {
            path: '/home',
            name: 'home',
            component: Home,
            meta: {
                requiresAuth: true
            }
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
            path: '/information/:idPersona',
            name: 'informacion',
            component: Participante
        },
        {
            path: '/perfil/:idPersona',
            name: 'perfil',
            component: Perfil
        },
        {
            path: '/ajustes/:idPersona',
            name: 'ajustes',
            component: MenuAjustes
        },
        {
            path: '/search/:busqueda',
            name: 'search',
            component: Search
        },
        {
            path: '/coleccion',
            name: 'coleccion',
            component: MiColeccion
        }
    ],
})

/* router.beforeEach((to, from, next)=>{
    if (to.matched.some(rec => rec.meta.requiresAuth)) {  
        // Si no se ha iniciado sesión se redirecciona al login
        if (user) {
            next()
        } else {
            const login = router.push('/login')
            next(login)
        }
    }
    next()
}) */

export default router;