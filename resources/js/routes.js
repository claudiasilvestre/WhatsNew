import VueRouter from 'vue-router'
// import store from './store'
import Home from './components/home/Home.vue'
import Audiovisual from './components/audiovisual/Audiovisual.vue'
import Capitulo from './components/capitulo/Capitulo.vue'
import Participante from './components/Participante.vue'
import Registro from './components/auth/Registro.vue'
import Login from './components/auth/Login.vue'

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
        }
    ],
})

/* router.beforeEach((to, from, next)=>{
        console.log("Hola");
        axios.get('/api/user')
            .then(response => {
                if (to.matched.some(rec => rec.meta.requiresAuth)) {  
                const user = response.data; 
                console.log(user);
                if (user) {
                    next()
                } else {
                    const login = router.push('/login')
                    next(login)
                }
            }
            next()})
            .catch(error => { console.log(error.response) });
}) */

export default router;