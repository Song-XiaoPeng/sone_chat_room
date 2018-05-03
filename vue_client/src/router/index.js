import Vue from 'vue'
import Router from 'vue-router'
import chatroom from '@/components/chatroom'
import layout from '@/components/layout.vue'
import login from '@/components/user/login.vue'

Vue.use(Router)

export default new Router({
  mode: 'history',
  routes: [
    {
      path: '/',
      name: 'chatroom',
      component: chatroom
    },
    {
      path: '/home',
      name: 'home',
      component: layout,
      children: [
        
      ]
    },
    {
      path: '/login',
      name: 'login',
      component: login
    }
  ]
})
