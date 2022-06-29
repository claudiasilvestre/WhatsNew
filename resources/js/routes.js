import VueRouter from 'vue-router'
// import store from './store'
import Home from './components/home/Home.vue'
import Audiovisual from './components/audiovisual/Audiovisual.vue'
import Capitulo from './components/capitulo/Capitulo.vue'
import Participante from './components/Participante.vue'
import Registro from './components/auth/Registro.vue'
import Login from './components/auth/Login.vue'
import Perfil from './components/perfil/Perfil.vue'
import MenuAjustes from './components/perfil/MenuAjustes.vue'

const router = new VueRouter ({
    mode: 'history',
    routes: [
        {
            path: '/',
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
            path: '/media/:idAudiovisual/information/:idPersona',
            name: 'informacion',
            component: Participante
        },
        {
            path: '/register',
            name: 'register',
            component: Registro
        },
        {
            path: '/login',
            name: 'login',
            component: Login
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