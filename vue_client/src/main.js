// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import FastClick from 'fastclick'
import App from './App'
import router from './router'
import ajax from './api/index.js'
import  { AlertPlugin } from 'vux'
import { fetchToken } from './api/tools.js'
Vue.use(AlertPlugin)

FastClick.attach(document.body)

Vue.prototype.ajax = ajax
Vue.prototype.notify = (obj) => {
  obj.title = obj.title == undefined ? '提示' : obj.title
  Vue.$vux.alert.show({
    title: obj.title,
    content: obj.msg,
    onShow () {

    },
    onHide () {

    }
  })
}

Vue.config.productionTip = false

router.beforeEach((to,from,next) => {
  if(!fetchToken() && to.path != '/login'){
    next({path: '/login'})
  }
  next()
})

/* eslint-disable no-new */
new Vue({
  router,
  render: h => h(App)
}).$mount('#app-box')

