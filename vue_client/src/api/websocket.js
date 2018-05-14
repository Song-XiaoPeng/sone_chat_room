import { fetchUserInfo } from './tools.js'

const WS_HOST = "ws://ws.hellobirds.top:81"

let ws = {}

ws.create = () => {
    try{
        this.socket = new WebSocket(WS_HOST)
        this._init()
    }catch(e){
        this._reconnect()
    }
}

ws.sendMsg = (content,type) => {
    let msg = {
        content,
        type
    }
    this.socket.send(msg)
}

ws._init = () => {
    this.socket.onopen = this.onopen
    this.socket.onmessage = this.onmessage
    this.socket.onclose = this.onclose
    this.socket.onerror = this.onerror
}

ws._reconnect = () => {
    this.create()
}

ws._hertCheck = () => {
    timeout: 60000//60秒
    timeoutObj: null
    serverTimeoutObj: null
    reset: () => {
        clearTimeout(this.timeoutObj)
        clearTimeout(this.serverTimeoutObj)
        return this
    }
    start: () => {
        this.timeoutObj = setTimeout(() => {
            //这里发送一个心跳，后端收到后，返回一个心跳消息，
            //onmessage拿到返回的心跳就说明连接正常
            this.socket.send("HeartBreak")
            this.serverTimeoutObj = setTimeout(() => {//如果超过一定时间还没重置，说明后端主动断开了
                this.socket.close();//如果onclose会执行reconnect，我们执行ws.close()就行了.如果直接执行reconnect 会触发onclose导致重连两次
            }, this.timeout)
        }, this.timeout)
    }
}

ws.onopen = () => {
    // 用户登陆发送登陆信息
    let userInfo = fetchUserInfo()
    let msg = {
        type: 'login',
        uid: userInfo.uid
    }
    this.socket.send(JSON.stringify(msg))

    //发送心跳
    this._hertCheck.reset().start()
}

ws.onmessage = () => {
    //心跳重置
    this._hertCheck.reset().start()
}

ws.onclose = () => {
    window.setTimeout(() => {
        this.reconnect
    },2000) //延迟2s重连，避免过多的请求
}

ws.onerror = (e) => {
    console.log(e)
}

export default ws;

