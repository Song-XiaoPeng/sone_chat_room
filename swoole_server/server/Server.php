<?php

use server\common\BaseServer;

class Server extends BaseServer
{
    protected $server;
    protected $storage;

    public function onMessage(swoole_websocket_server $server, $frame)
    {
        $data = json_decode($frame->data, true);
        $msg_type = $data['msg_type'];
        $client_id = $frame->fd;
        $this->$msg_type($client_id, $data);
    }

    //用户登陆
    public function c_login($client_id, $msg)
    {
        $uid = $msg['uid'];
        $this->storage->login($client_id, $uid);
        $send_msg = [
            'uid' => $uid,
            'msg_type' => 'login'
        ];
        $this->send2friendsAndGroupMembers($client_id, $uid, $send_msg);
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

    }

    //发送消息
    public function send($client_id, $msg)
    {
        $this->server->push($client_id, $msg);
    }

    //向群聊成员发送消息
    public function send2group($current_client_id, $group_id, $msg)
    {
        $onlineList = $this->storage->getOnlineGroupUserList($group_id);
        foreach ($onlineList as $v) {
            if ($current_client_id == $v) continue;
            $this->sendJson($v, $msg);
        }
    }

    //向好友和群聊成员发送消息
    public function send2friendsAndGroupMembers($current_client_id, $uid, $msg)
    {
        $onlineList = $this->storage->getFriendsAndGroupMembers($uid);
        foreach ($onlineList['groups'] as $v) {
            $this->send2group($current_client_id, $v, $msg);
        }
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

    public function sendJson($client_id, $msg)
    {
        $this->server->push($client_id, json_encode($msg, true));
    }

    //给用户发消息
    public function send2User($current_client_id, $uid, $msg)
    {
        //找到用户对应的client_id

    }

    protected function initModule()
    {
        parent::initModule();
        $this->server = new swoole_websocket_server($this->config['ws']['host'], $this->config['ws']['port']);
        $this->storage = new Storage();
        $this->server->set([]);
        $this->server->on('finish', [$this, 'onFinish']);
        $this->server->on('open', [$this, 'onOpen']);
        $this->server->on('message', [$this, 'onMessage']);
        $this->server->on('close', [$this, 'onClose']);
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

