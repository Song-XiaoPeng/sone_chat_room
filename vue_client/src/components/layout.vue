<style scoped>
    .content {
        margin-top: 46px;
    }
</style>
<template>
    <div style="height:100%;">
        <h3>用户列表</h3>
        <template  v-for="(val1,key1) in userList">
            <div>
                <input type="checkbox" @click="userChecked(val1.uid)"> {{ val1.nickname }} 
            </div>
            <div>
                <input type="text" v-model="val1.content">
            </div>
            <br>
        </template>

        <button @click="sendMsg()">发送消息</button>
    </div>
</template>
<script>
import { createDB, getData } from '../api/WebDB';
import Bus from '@/api/bus.js'
import { socketHost } from '@/api/socket.js'
import  localforage  from 'localforage'

export default {
  data() {
      return {
          msgs: [],
          userList: [],
          ws: '',
          uids: []
      }
  },
  methods: {
      sendMsg() {
        let msg1 = [];
        let uids = this.uids
        this.userList.forEach(ele => {
            if(uids.indexOf(ele.uid) >= 0){
                msg1.push({
                    uid:ele.uid,
                    msg:ele.content
                })
            }
        })  
        this.ws.send(JSON.stringify({msg:msg1,msg_type:'demo'}))
      },
      userChecked(val) {
          //不存在就添加，存在就删除
          var start = this.uids.indexOf(val)
          if(start >= 0){
            this.uids.splice(start,1)
          } else {
            this.uids.push(val)
          }
          console.log(this.uids)
      },
      onMessage(event) {
        let msg = JSON.parse(event.data)
        let msg_type = msg.msg_type
        
        this.notify({msg:msg.msg})
      },
      reconnect(){
        this.createWs()
      },
      onClose(msg){
        setTimeout(() => {
            this.reconnect();
        },2000)// 延迟2s重连，避免过多的请求
      },
      onOpen(event) {
        
      },
      createWs(){
          try{
            this.ws = new WebSocket(socketHost)
            this.initWs()      
          }catch(e){
              console.log(e)
              this.reconnect()
          }
      },
      initWs() {
          console.log(11111)
        this.ws.onmessage = this.onMessage
        this.ws.onclose = this.onClose
        this.ws.onopen = this.onOpen
      }
  },
  created() {
      localforage.setItem('sone','111').then(function(){
          console.log(123123)
      })

      let _this = this
      createDB("users",'userList','uid')

      Bus.$on('createDB',(val)=>{
          _this.db = val
          getData(this.db,'user_list')
      })

      Bus.$on('user_list',function(val){
        val.forEach(element => {
            element.content = ''
        });
        _this.userList = val
      })
      this.createWs()
  },
  components: {
  }
}
</script>

