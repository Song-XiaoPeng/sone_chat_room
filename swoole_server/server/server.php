<?php

use Medoo\Medoo;

require_once "../../vendor/autoload.php";

//初始化数据库
// 初始化配置
$database = new Medoo([
    'database_type' => 'mysql',
    'database_name' => 'timeline',
    'server' => 'localhost',
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8'
]);


$server = new swoole_websocket_server("0.0.0.0", 81);
$server->set([
//    'worker_num' => 4
]);

$server->on('finish', function ($server, $task_id, $data) {

});

$server->on('open', function (swoole_websocket_server $server, $request) use ($database) {
    //握手成功后会触发onOpen事件，表示连接已就绪，onOpen函数中可以得到$request对象，包含了Http握手的相关信息，如GET参数、Cookie、Http头信息等。
    echo "client_id: {$request->fd} 连接成功"."\n";
    $msg = [
        'client_id' => $request->fd,
        'msg_type' => 'onconnect'
    ];
    $server->push($request->fd,json_encode($msg));
});


$server->on('message', function (swoole_websocket_server $server, $frame) use ($database) {
    //$frame 是swoole_websocket_frame对象，包含了客户端发来的数据帧信息
    //onMessage回调必须被设置，未设置服务器将无法启动
    //客户端发送的ping帧不会触发onMessage，底层会自动回复pong包

    /*
     * $frame->fd，客户端的socket id，使用$server->push推送数据时需要用到
       $frame->data，数据内容，可以是文本内容也可以是二进制数据，可以通过opcode的值来判断
       $frame->opcode，WebSocket的OpCode类型，可以参考WebSocket协议标准文档
       $frame->finish， 表示数据帧是否完整，一个WebSocket请求可能会分成多个数据帧进行发送（底层已经实现了自动合并数据帧，现在不用担心接收到的数据帧不完整）
     */
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
});

$server->on('close', function ($server, $fd) use ($database) {
    //查找userinfo
    // $uid = $database->get('client_user','uid',[
    //     'client_id' => $fd
    // ]);
    // var_dump($uid);
    // if(empty($uid)) return;
    // $sql = "select a.id,a.nickname,b.save_name from user a left join attachment b on a.avator=b.id where b.uid=".$uid;
    // $userInfo = $database->query($sql)->fetch(PDO::FETCH_ASSOC);
    // var_dump($userInfo);
    // foreach ($server->connections as $fd1) {
    //     if($fd1 == $fd) continue;
    //     //排除自己
    //     $msg = [
    //         'msg_type' => "close",
    //         'userInfo' => $userInfo,
    //     ];
    //     $server->push($fd1, json_encode($msg));
    // }
    $database->delete('client_user', ['client_id' => $fd]);
    echo "client {$fd} closed\n";
    echo "删除client_id:{$fd}, 受影响行数：" . $database->rowCount() . "数据库【client_user】\n";
});

$server->start();

//获得历史聊天记录
function getHistoryMsg($db) {
    $db->select('chat');
}
/*
$server->on('workerStart', function ($server, $workerId) {
    $client = new swoole_redis;
    $client->on('message', function (swoole_redis $client, $result) use ($server) {
        if ($result[0] == 'message') {
            foreach($server->connections as $fd) {
                $server->push($fd, $result[1]);
            }
        }
    });
    $client->connect('127.0.0.1', 6379, function (swoole_redis $client, $result) {
        $client->subscribe('msg_0');
    });
});

$server->on('open', function ($server, $request) {

});

$server->on('message', function (swoole_websocket_server $server, $frame) {
    $server->push($frame->fd, "hello");
});

$server->on('close', function ($serv, $fd) {

});

$server->start();*/
