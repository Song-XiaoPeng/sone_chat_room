import Vue from 'vue'
import Router from 'vue-router'
import chatroom from '@/components/chatroom/chatroom'
import layout from '@/components/layout.vue'
import demo from '@/components/demo.vue'
import login from '@/components/user/login.vue'
import group from '@/components/group/group'
import friends from '@/components/friends/friends'
import interest from '@/components/interest/interest'
import profile from '@/components/user/profile'

Vue.use(Router)

export default new Router({
  mode: 'history',
  routes: [
    {
      path: '/',
      name: 'index',
      redirect: '/home'
    },
    {
      path: '/home',
      component: layout,
      children: [
        {
          path: 'group',
          name: 'group',
          component: group,
        },
        {
          path: 'friends',
          name: 'friends',
          component: friends,
        },
        {
          path: 'interest',
          name: 'interest',
          component: interest,
        },
        {
          path: 'profile',
          name: 'profile',
          component: profile,
        }
      ]
    },
    {
      path: '/chatroom',
      name: 'chatroom',
      component: chatroom
    },
    {
      path: '/demo',
      name: 'demo',
      component: demo
    },
    {
      path: '/login',
      name: 'login',
      component: login
    }
  ]
})
