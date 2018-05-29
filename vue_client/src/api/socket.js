
import { fetchUserInfo } from './tools.js'

export const socketHost = 'ws://ws.hellobirds.top:81'

export const socket = new WebSocket(socketHost);    

const userInfo = fetchUserInfo()

// Connection opened
socket.addEventListener('open', onOpen);

function onOpen(event) {
    let msg = {}
    msg.uid = userInfo.uid
    msg.msg_type = 'connect'
    socket.send(JSON.stringify(msg));
}

