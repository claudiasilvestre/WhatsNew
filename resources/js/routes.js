import VueRouter from 'vue-router'
import store from './store'
import Home from './components/home/Home.vue'
import Audiovisual from './components/audiovisual/Audiovisual.vue'
import Capitulo from './components/capitulo/Capitulo.vue'
import Participante from './components/Participante.vue'
import Perfil from './components/perfil/Perfil.vue'
import MenuAjustes from './components/perfil/MenuAjustes.vue'
import Busqueda from './components/Busqueda.vue'
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
            component: Audiovisual,
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/media/:idAudiovisual/episode/:idCapitulo',
            name: 'capitulo',
            component: Capitulo,
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/information/:idPersona',
            name: 'informacion',
            component: Participante,
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/perfil/:idPersona',
            name: 'perfil',
            component: Perfil,
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/ajustes/:idPersona',
            name: 'ajustes',
            component: MenuAjustes,
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/busqueda/:busqueda',
            name: 'busqueda',
            component: Busqueda,
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/coleccion',
            name: 'coleccion',
            component: MiColeccion,
            meta: {
                requiresAuth: true
            }
        }
    ],
})

router.beforeEach(async (to, from, next) => {
    await store.dispatch('currentUser/getUser')
    const user = store.state.currentUser.user

    if (to.name === 'index' && Object.keys(user).length > 0) {
        const home = router.push('/home')
        next(home)
        
    } else if (to.matched.some(rec => rec.meta.requiresAuth)) {  
        // Si no se ha iniciado sesión se redirecciona al login
        if (Object.keys(user).length > 0) {
            next()
        } else {
            const login = router.push('/')
            next(login)
        }
    }
    next()
})

export default router;