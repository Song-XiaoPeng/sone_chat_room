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
        <x-input title="昵称" v-model="nickname" required placeholder="请输入昵称" class="x_input"></x-input>
        <x-input title="密码" v-model="password" type="password" required placeholder="请输入密码" class="x_input"></x-input>
        <x-button type="primary" @click.native="login">登陆</x-button>
    </group>
  </div>
</div>
</template>

<script>
import { Group,XInput,XButton,Alert,XImg } from 'vux'
import { setStorage,USER_INFO_KEY,TOKEN_KEY } from '../../api/tools.js'

export default {
  data() {
      return {
          value: '2018-04-29',
          nickname: '',
          password: '',
          show2: false
      }
  },
  methods:{
      login() {
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
                this.$router.push('/')
            },
            error:(res) => {
                this.notify({msg:res})
            }
        })
      }
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

