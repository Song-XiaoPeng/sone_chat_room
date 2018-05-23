<?php

use server\common\BaseStorage;

class Storage extends BaseStorage
{
    const ONLINE_USER = "chat:online_user";
    const ONLINE_CLIENT = "chat:online_client";
    const CLIENT = ":client";

    const GROUP_MEMBERS = ":group_members";
    const GROUP_MSG = ":group_msg";

    //用户登陆
    public function login($client_id, $userInfo)
    {
        //记录登陆状态
        $this->redis->set(self::CLIENT . $client_id, json_encode($userInfo));
        $this->redis->sadd(self::ONLINE_USER, $userInfo['uid']);
        $this->redis->sadd(self::ONLINE_CLIENT, $client_id);
    }

    //用户登出
    public function logout($client_id)
    {
        $this->redis->del(self::CLIENT . $client_id);
        $this->redis->rem(self::ONLINE_CLIENT, $client_id);
    }

    /* 群聊 */
    //用户加入群聊
    public function joinGroup($group_id, $uid)
    {
        $this->redis->zadd(self::GROUP_MEMBERS . $group_id, time(), $uid);
        $data = [
            'group_id' => $group_id,
            'uid' => $uid
        ];
        $this->db->insert('user_group_relationship', $data);
    }

    //用户退出群聊
    public function delGroupMember($group_id, $uid)
    {
        $this->redis->zrem(self::GROUP_MEMBERS . $group_id, $uid);
        $where = [
            'group_id' => $group_id,
            'uid' => $uid
        ];
        $this->db->insert('user_group_relationship', $where);
    }

    //获得群聊的用户列表
    public function getGroupUserList($group_id)
    {
        $group_members = $this->redis->zrange(self::GROUP_MEMBERS . $group_id, 0, -1, 'WITHSCORES');
        $online_user = $this->getOnLineUserList();
    }

    //保存群聊历史记录
    public function sendGroupMsg($group_id, $msg)
    {
        //        $this->redis->zadd(self::GROUP_MSG . $group_id, time(), json_encode($msg));
        $data = [
            'group_id' => $group_id,
            'msg' => $msg
        ];
        $this->db->insert('group_msg', $data);
    }

    //获得群聊历史记录
    public function getGroupHistory($group_id)
    {
        return $this->db->get(['group_id' => $group_id])->orderBy('create_time asc');
    }

    /* 单聊 */
    // 获得两个用户单聊会话id
    public function getSingleChatSession($send_uid, $recv_uid)
    {
        $uid_data = compact('send_uid', 'recv_uid');
        $where = ['send_uid' => $uid_data, 'recv_uid' => $uid_data];
        $rs = $this->db->get('single_chat_session', $where);
        if ($rs) {
            return $rs;
        }
        $data = [
            'session_id' => md5(time()),
            'send_uid' => $send_uid,
            'recv_uid' => $recv_uid
        ];
        return $this->db->insert('single_chat_session', $data);
    }

    //发送单聊历史记录
    public function sendSingleChatMsg($session_id, $msg)
    {
        $data = [
            'session_id' => $session_id,
            'send_uid' => $msg['send_uid'],
            'recv_uid' => $msg['recv_uid'],
            'msg' => $msg
        ];
        $this->db->insert('single_chat_msg', $data);
    }

    //获得单聊历史记录
    public function getSingleChatMsgHistory($session_id)
    {
        return $this->db->get('single_chat_msg', ['session_id' => $session_id]);
    }

    /**
     * 通用方法
     * @return mixed
     */
    //获得在线人员列表
    public function getOnLineUserList()
    {
        return $this->redis->smembers(self::ONLINE_USER);
    }

    //批量获得用户列表
    public function getUsers()
    {

    }

    //获得单个用户
    public function getUser()
    {

    }

    //获得在线的客户端列表
    public function getOnLineClientList()
    {
        return $this->redis->smembers(self::ONLINE_CLIENT);
    }

    //获得好友列表
    public function getFriends($uid)
    {
        $res = $this->db->get('user_friends_relationship', ['uid' => $uid]);
        //获得登陆状态
        $online_list = $this->getOnLineUserList();
        return $res;
    }
}