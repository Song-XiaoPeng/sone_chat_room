<style lang="less">
@import '~vux/src/styles/1px.less';
.content {
  padding: 15px;
  overflow-y: auto;
  margin-bottom:40px;
}

.header {
  background: #05ce91;
  height: 40px;
  line-height: 40px;
  text-align: center;
  font-weight: bold;
}

.content ul, .content li {
  list-style: none;
}

.content li {
  position: relative;
}

.avator {
  position: absolute;
  top:0px;
  width:40px;
}

.msg span{
  background: white;
  border-radius: 6px;
  padding: 5px;
  display: inline-block;
  margin-bottom: 5px;
}

.title {
  margin-bottom: 5px;
}

.left .avator {
  left: 0px;
}

.right .avator {
  right: 0px;
}

.left .title {
  margin-left: 42px;
}

.left .msg {
  margin-left: 42px;
}

.right .title {
  text-align: right;
  margin-right: 42px;
}

.right .msg {
  text-align: right;  
  margin-right: 42px;
}

.sendMsg {
  position: fixed;
  bottom: 0;
  width: 100%;
  padding: 5px;
}
</style>

<template>
  <div class="container1">
    <div class="header">聊天室</div>
    <div class="content">
      <toast v-model="showConnectValue" type="text" :time="2000" width="10em" is-show-mask :text="showConnectText" position="top"></toast>
      <ul>
        <template v-for="(val,idx) in msgList">
          <li class="left" v-if="val.uid === userinfo.uid">
            <img src="../../assets/logo.png" alt="" class="avator">
            <div class="title">
              <span>{{ userinfo.nickname }}</span>
              <span>{{ val.sendTime }}</span>
            </div>
            <div class="msg">
              <span>{{ val.msg }}</span>
            </div>
          </li>
          <li class="right" v-else>
            <img src="../../assets/logo.png" alt="" class="avator">
            <div class="title">
              <span>{{ val.userInfo.nickname }}</span>
              <span>{{ val.sendTime }}</span>
            </div>
            <div class="msg">
              <span>{{ val.msg }}</span>
            </div>
          </li>
        </template>
      </ul>
    </div>
    <div class="sendMsg">
      <group>
        <x-input v-model="msg" novalidate :show-clear="false" style="height:20px;">
          <x-button slot="right" type="primary" mini @click.native="sendMessage">发送</x-button>
        </x-input>
      </group>
    </div>
  </div>
</template>

<script>
import { Divider,Flexbox,FlexboxItem,XButton,XInput,Group,ViewBox,XTextarea,Toast } from 'vux'
import { socket } from '../../api/socket.js'
import { fetchUserInfo,getNowFormatDate } from '../../api/tools.js'

export default {
  data() {
    return {
      ajaxLock: false,
      userinfo: {},
      msgList: [],
      msg: '',
      showConnectValue: false,
      showConnectText: 'Hello, Welcome!'
    }
  },
  methods: {
    showConnectMsg(userInfo, msg_type) {
      this.showConnectValue = true      
      let notice = ''
      switch(msg_type) {
        case 'connect':        
          notice = '登陆群聊成功，欢迎！'
        break;
        case 'close':        
          notice = '退出群聊'
      }
      this.showConnectText = '用户' + userInfo.nickname + notice
    },
    onClose(event) {
    },
    sendMessage() {
      if (this.ajaxLock) return
      this.ajaxLock = true
      if (this.msg == '') return
      let msg =  {
        uid: this.userinfo.uid,
        sendTime: getNowFormatDate(),
        msg: this.msg,
        msg_type: 'group_message'
      }
      this.msgList.push(msg)
      this.socket.send(JSON.stringify(msg))
      this.msg = ''
      this.ajaxLock = false
    },
    onMessage(event) {
      let msg = JSON.parse(event.data)
      // console.log(msg)
      switch(msg.msg_type) {
        case 'connect':        
          this.showConnectMsg(msg.userInfo, 'connect')
        break;
        case 'close':        
          this.showConnectMsg(msg.userInfo, 'close')
        break;
        case 'message':
          this.msgList.push({
            uid: msg.uid,
            msg: msg.msg,
            sendTime: msg.send_time,
            userInfo: msg.userInfo
          })
        break;
      }
    }
  },
  created() {
    this.socket = socket
    this.socket.onmessage = this.onMessage
    this.socket.onclose = this.onClose
    this.userinfo = fetchUserInfo()

    document.onkeydown = (e) => {
        if(e.keyCode == 13) {
            this.sendMessage()
        }
    }
  },
  components: {
    Divider,
    Flexbox,
    FlexboxItem,
    XButton,
    XInput,
    Group,
    ViewBox,
    XTextarea,
    Toast 
  }
}
</script>


