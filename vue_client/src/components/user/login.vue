<style scoped>
    .content {
    }

    .icon_container {
        text-align: center;
    }

    .icon_container span {
        display: block;
        font-size: 14px;
    }

    .icon {
        border-radius: 50%;
        width:100px;
        margin-top:70px;
    }

    .form {

    }

    .x_input {

    }
</style>

<template>
<div class="content">
  <div class="icon_container">
    <x-img src="/static/image/ironcat.jpg" class="icon"></x-img>
    <span>三人行必有我师焉</span>
  </div>
  <div class="form">
    <group>
        <x-input title="昵称" v-model="nickname" required :min="3" placeholder="请输入昵称，最少三位" class="x_input"></x-input>
        <x-input title="密码" v-model="password" type="password" :min="3" required placeholder="请输入密码，最少三位" class="x_input"></x-input>
        <x-button type="primary" @click.native="login" @keyup.native.enter="login">登陆</x-button>
    </group>
  </div>
</div>
</template>

<script>
import { Group,XInput,XButton,Alert,XImg } from 'vux'
import { setStorage,USER_INFO_KEY,TOKEN_KEY,fetchUserInfo } from '../../api/tools.js'
import { openDB,createDB,insertData,getData } from '../../api/WebDB.js'
import Bus from '@/api/bus.js'

export default {
  data() {
      return {
          value: '2018-04-29',
          nickname: '',
          password: '',
          show2: false,
          ajaxLock: false,
          db: ''
      }
  },
  methods:{
      login() {
        if(this.nickname == '' || this.password == '') {
            this.notify({msg:"账号或者密码不能为空！"})
            return
        }
        if (this.ajaxLock) return 
        this.ajaxLock = true
        this.ajax.login({
            data: {
                nickname: this.nickname,
                password: this.password,
                type: 'backend'
            },
            success:(res) => {
                this.notify({msg:"登陆成功!"})
                setStorage(USER_INFO_KEY,res)
                setStorage(TOKEN_KEY,res.access_token)
                insertData(this.db,'user_list',fetchUserInfo())
                Bus.$emit("user_login",fetchUserInfo())
                this.$router.push('/home')
            },
            error:(res) => {
                this.ajaxLock = false
                this.notify({msg:res})
            }
        })
      }
  },
  created() {
    let _this = this
    document.onkeydown = (e) => {
        let key = window.event.keyCode;
        if(key==13) {
            this.login()
        }
    }
    createDB("users",'user_list','uid')
    Bus.$on('createDB',function(val){
      _this.db = val
    })
  },
  components: {
      Group,
      XInput,
      XButton,
      Alert,
      XImg  
  }
}
</script>

