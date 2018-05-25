<?php

use server\common\BaseStorage;

class Storage extends BaseStorage
{
    const ONLINE_USER = "chat:online_user";
    const ONLINE_CLIENT = "chat:online_client";
    const CLIENT = ":client";

    const GROUP_MEMBERS = ":group_members";
    const GROUP_MSG = ":group_msg";

    //建立连接
    public function onOpen($client_id)
    {
        $this->redis->sadd(self::ONLINE_CLIENT, $client_id);
    }

    //断开连接
    public function onClose($client_id)
    {
        $this->redis->rem(self::ONLINE_CLIENT, $client_id);
    }

    //用户登陆
    public function login($client_id, $uid)
    {
        //记录登陆状态
        $this->redis->set(self::CLIENT . $client_id, $uid);
        $this->redis->sadd(self::ONLINE_USER, $uid);
    }

    //用户登出
    public function logout($client_id)
    {
        $this->redis->del(self::CLIENT . $client_id);
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
        $this->db->insert('u_user_group_relationship', $data);
    }

    //用户退出群聊
    public function delGroupMember($group_id, $uid)
    {
        $this->redis->zrem(self::GROUP_MEMBERS . $group_id, $uid);
        $where = [
            'group_id' => $group_id,
            'uid' => $uid
        ];
        $this->db->insert('u_user_group_relationship', $where);
    }

    //获得当前在线群聊的用户列表
    public function getOnlineGroupUserList($group_id)
    {
//        $res = $this->db->get('u_user_group_relationship','uid', ['group_id' => $group_id]);
//        $online_user = $this->getOnLineUserList();
        $group_members = $this->redis->zrange(self::GROUP_MEMBERS . $group_id, 0, -1, 'WITHSCORES');
        $online_user = $this->getOnLineUserList();
        foreach ($group_members as &$v) {
            if (in_array($v['uid'], $online_user)) {
                $v['user_status'] = 'online';
            } else {
                $v['user_status'] = 'offline';
            }
        }
        unset($v);
        return $group_members;
    }

    //获得用户的所有在线群聊分组成员和在线好友
    public function getFriendsAndGroupMembers($uid)
    {
        $groups = $this->db->select('u_user_group_relationship', 'group_id', ['uid' => $uid]);
        $users = $this->db->select('u_user_friends_relationship', 'friend_uid', ['uid' => $uid]);
        $groups = array_column($groups,'group_id');
        $users = array_column($users,'friend_uid');
        $res = array_merge($users,$groups);
        return compact('res');
    }

    //保存群聊历史记录
    public function saveGroupMsg($group_id, $send_uid, $msg)
    {
        //        $this->redis->zadd(self::GROUP_MSG . $group_id, time(), json_encode($msg));
        $data = [
            'group_id' => $group_id,
            'send_uid' => $send_uid,
            'msg' => json_encode($msg)
        ];
        $this->db->insert('g_group_chat_msg', $data);
    }

    //获得群聊历史记录
    public function getGroupHistory($group_id)
    {
        return $this->db->select('g_group_chat_msg', '*', [
            'group_id' => $group_id,
            'ORDER' => 'created_time desc'
        ]);
    }

    /* 单聊 */
    // 获得两个用户单聊会话id
    public function getSingleChatSession($send_uid, $recv_uid)
    {
        $uid_data = compact('send_uid', 'recv_uid');
        $where = ['OR' => ['send_uid' => $uid_data, 'recv_uid' => $uid_data], 'AND' => ['is_del' => 0]];
        $rs = $this->db->get('s_single_chat_session', 'id', $where);
        if ($rs) {
            return $rs;
        }
        try {
            $this->db->insert('s_single_chat_session', $uid_data);
            return $this->db->id();
        } catch (\Exception $e) {
            $this->log->writeLog('单聊创建会话失败');
            return $this->db->get('s_single_chat_session', 'id', $where);
        }
    }

    //保存单聊历史记录
    public function saveSingleChatMsg($session_id, $msg)
    {
        $data = [
            'session_id' => $session_id,
            'send_uid' => $msg['send_uid'],
            'recv_uid' => $msg['recv_uid'],
            'msg' => $msg
        ];
        $this->db->insert('s_single_chat_msg', $data);
    }

    //获得单聊历史记录
    public function getSingleChatMsgHistory($session_id)
    {
        return $this->db->select('s_single_chat_msg', '*', [
            'session_id' => $session_id,
            'LIMIT' => '10',
            'ORDER' => 'created_time DESC'
        ]);
    }

    /**
     * 通用方法
     *
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
        $res = $this->db->select('u_user_friends_relationship', '*', ['uid' => $uid]);
        //获得登陆状态
        $online_list = $this->getOnLineUserList();
        array_walk($res, function (&$v) use ($online_list) {
            if (in_array($v['friend_uid'], $online_list)) {
                $v['user_status'] = 'online';
            } else {
                $v['user_status'] = 'offline';
            }
        });
        return $res;
    }
}