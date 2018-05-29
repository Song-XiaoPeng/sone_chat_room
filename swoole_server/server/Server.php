<?php

use server\common\BaseServer;

class Server extends BaseServer
{
    protected $server;
    protected $storage;
    const MSG_TYPE = [
        1000 => 'login',
        1001 => 'logout',
        1002 => 'broadcast', //广播
        1003 => 'init_chatroom', //初始化聊天室

        1100 => 'join_group', //用户加入群聊
        1101 => 'leave_group', //用户退出群聊
        1102 => 'group_msg',//发送群聊消息
        1103 => '',
        1104 => '',

        1200 => 'single_msg',//发送单聊消息
    ];

    public function onMessage(swoole_websocket_server $server, $frame)
    {
        $data = json_decode($frame->data, true);
        $msg_type = $data['msg_type'];
        $method = 'c_' . $msg_type;
        $client_id = $frame->fd;
        $this->$method($client_id, $data);
    }

    //用户登陆
    public function c_login($client_id, $msg)
    {
        $uid = $msg['uid'];
        $this->storage->login($client_id, $uid);
        $this->loginNotify($client_id, $uid);
    }

    //用户退出
    public function c_logout($client_id, $msg)
    {
        $uid = $msg['uid'];
        $this->storage->login($client_id, $uid);
        $this->logoutNotify($client_id, $uid);
    }

    //用户加入群聊
    public function c_join_group($client_id, $msg)
    {
        $group_id = $msg['group_id'];
        $send_msg = $msg['msg'];
        $this->send2group($client_id, $group_id, $send_msg);
    }

    //用户发送群聊消息
    public function c_group_msg($client_id, $msg)
    {
        $group_id = $msg['group_id'];
        $send_uid = $msg['send_uid'];
        $send_msg = $msg['msg'];
        $this->storage->saveGroupMsg($group_id, $send_uid, $send_msg);
        $this->send2group($client_id, $group_id, $send_msg);
    }

    //用户发送单聊消息
    public function c_single_msg($client_id, $msg)
    {
        $send_uid = $msg['send_uid'];
        $recv_uid = $msg['recv_uid'];
        $session_id = $msg['session_id'] ?: $this->storage->getSingleChatSession($send_uid, $recv_uid);
        $send_msg = $msg['msg'];
        $this->storage->saveSingleChatMsg($session_id, $send_msg);
        $this->send2User($client_id, $recv_uid, $send_msg);
        $this->send2User($client_id, $send_uid, $send_msg);
    }

    //初始化聊天室 获得用户列表 用户信息 聊天历史记录等
    public function c_init_chatroom($client_id, $msg)
    {
        $msg_types = $msg['msg_types'];
        foreach ($msg_types as $msg_type) {
            $method = 'i_' . $msg_type;
            $this->$method($client_id, $msg);
        }
    }

    //初始化聊天室：获得好友列表
    public function i_friends_list($client_id,$msg)
    {
        $uid = $msg['uid'];
        $uids = $this->storage->getFriendsList($uid);

    }

    //向群聊成员发送消息
    public function send2group($current_client_id, $group_id, $msg)
    {
        $onlineList = $this->storage->getOnlineGroupUserList($group_id);
        foreach ($onlineList as $v) {
            if ($current_client_id == $v) continue;
            $this->send2User($current_client_id, $v, $msg);
        }
    }

    //给用户发消息
    public function send2User($current_client_id, $uid, $msg)
    {
        //找到用户对应的client_id
        $onlineList = $this->storage->getOnlineUid2Client($uid); //[1,2,3]
        foreach ($onlineList as $v) {
            if ($current_client_id == $v) continue;
            $this->sendJson($v, $msg);
        }
    }

    //向好友和群聊成员发送消息
    public function send2friendsAndGroupMembers($current_client_id, $uid, $msg)
    {
        $users = $this->storage->getFriendsAndGroupMembers($uid);
        foreach ($users as $uid) {
            $this->send2User($current_client_id, $uid, $msg);
        }
    }

    //登陆通知
    public function loginNotify($current_client_id, $uid)
    {
        $msg = [
            'msg_type' => 'login',
            'uid' => $uid
        ];
        $this->send2friendsAndGroupMembers($current_client_id, $uid, $msg);
    }

    //退出通知
    public function logoutNotify($current_client_id, $uid)
    {
        $msg = [
            'msg_type' => 'logout',
            'uid' => $uid
        ];
        $this->send2friendsAndGroupMembers($current_client_id, $uid, $msg);
    }

    //向所有在线人员广播消息
    public function broadcast($current_client_id, $msg)
    {
        $onlineList = $this->storage->getOnLineClientList();
        foreach ($onlineList as $v) {
            if ($current_client_id == $v) continue;
            $this->sendJson($v, $msg);
        }
    }

    //发送消息
    public function send($client_id, $msg)
    {
        $this->server->push($client_id, $msg);
    }

    public function sendJson($client_id, $msg)
    {
        $this->server->push($client_id, json_encode($msg, true));
    }

    protected function initModule()
    {
        parent::initModule();
        $this->server = new swoole_websocket_server($this->config['ws']['host'], $this->config['ws']['port']);
        $this->server->set([
//            'worker_num' => 4,
            'daemonize' => true,
        ]);
        $this->server->on('finish', [$this, 'onFinish']);
        $this->server->on('open', [$this, 'onOpen']);
        $this->server->on('message', [$this, 'onMessage']);
        $this->server->on('close', [$this, 'onClose']);
        $this->server->start();
        $this->storage = new Storage();
    }

    public function onOpen(swoole_websocket_server $server, $request)
    {
        $log = "client_id: {$request->fd} 连接成功";
        $this->Log($log);
        $this->storage->onOpen($request->fd);

//        $server->push($request->fd,json_encode($msg));
    }

    public function onClose($server, $fd)
    {
        $this->storage->onClose($fd);
        $this->log("client {$fd} closed\n");
    }

    public function onFinish($server, $fd)
    {
        $this->log("client {$fd} finished\n");
    }
}

