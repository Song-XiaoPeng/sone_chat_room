<?php

class Swoole
{
    protected $server;

    public function __construct()
    {
        $this->server = new swoole_websocket_server("0.0.0.0", 81);
    }

    public function onOpen(swoole_websocket_server $server, $request)
    {
        echo "client_id: {$request->fd} 连接成功"."\n";
        $msg = [
            'client_id' => $request->fd,
            'msg_type' => 'onconnect'
        ];
        $server->push($request->fd,json_encode($msg));
    }

    public function onMessage()
    {
        $data = json_decode($frame->data, true);

        $data['client_id'] = $frame->fd;
        switch ($data['msg_type']) {
            case "connect":
                if(empty($data['uid'])){
                    return;
                }
                $database->insert('client_user', [
                    'client_id' => $data['client_id'],
                    'uid' => $data['uid']
                ]);
                echo "uid:【" . $data['uid'] . "】登陆了，client_id:【" . $data['client_id'] . "】\n";
                echo "插入数据库【client_user】自增id：【" . $database->id() . "】\n";
                //查找userinfo
                $userInfo = $database->get('user',[
                    'nickname','id','avator'
                ],[
                    'id'=>$data['uid']
                ]);
                var_dump($data['uid'],$userInfo);
                $msg = [
                    'msg_type' => 'connect',
                    'userInfo' => $userInfo
                ];
                foreach ($server->connections as $fd) {
                    $server->push($fd, json_encode($msg));
                }
                break;
            case "message":
                $sendTo = $data['sendTo'];
                $database->insert('chat_message', [
                    'uid' => $data['uid'],
                    'sendTo' => $data['sendTo'],
                    'msg' => $data['msg'],
                    'createtime' => time(),
                    'msg_type' => 1
                ]);
                echo "uid:【" . $data['uid'] . "】发送消息给，sendToUid:【" . $data['sendTo'] . "】\n";
                echo "插入数据库【chat_message】自增id：【" . $database->id() . "】\n";
                //获得接收者的client_id
                $client_ids = $database->select('client_user', 'client_id', ['uid' => $sendTo]);
                foreach ($server->connections as $fd) {
                    if (in_array($fd, $client_ids)) {
                        $msg = [
                            'msg' => $data['msg'],
                            'msg_type' => "message",
                            'send_time' => date("Y-m-d H:i:s")
                        ];
                        $server->push($fd, $msg);
                    }
                }
                break;
            case "group_message":
                $sendTo = 0;
                $database->insert('chat_message', [
                    'uid' => $data['uid'],
                    'sendTo' => $sendTo,
                    'msg' => $data['msg'],
                    'createtime' => time(),
                    'msg_type' => 2 //群聊
                ]);
                echo "uid:【" . $data['uid'] . "】发送群聊消息给，群组:【" . $sendTo . "】\n";
                echo "插入数据库【chat_message】自增id：【" . $database->id() . "】\n";
                //获得接收者的client_id
                $client_ids = $database->select('client_user', 'client_id', ['uid' => $sendTo]);
                //查找userinfo
                $uid = $database->get('client_user','uid',[
                    'client_id' => $frame->fd
                ]);
                var_dump($uid);
                $userInfo = $database->get('user',[
                    'nickname','id','avator'
                ],[
                    'id'=>$data['uid']
                ]);
                var_dump($userInfo);

                foreach ($server->connections as $fd) {
                    //排除自己
                    if($fd == $frame->fd) continue;
                    $msg = [
                        'uid' => $data['uid'],
                        'msg' => $data['msg'],
                        'msg_type' => "message",
                        'userInfo' => $userInfo,
                        'send_time' => date("Y-m-d H:i:s")
                    ];
                    $server->push($fd, json_encode($msg));
                }
                break;
        }
    }

    public function onClose($server, $fd)
    {
        $database->delete('client_user', ['client_id' => $fd]);
        echo "client {$fd} closed\n";
        echo "删除client_id:{$fd}, 受影响行数：" . $database->rowCount() . "数据库【client_user】\n";
    }
}