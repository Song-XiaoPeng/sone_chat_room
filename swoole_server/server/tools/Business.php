<?php
namespace server\tools;
use Medoo\Medoo;

class Business {
    protected $db;

    public function __construct($config) {
        $db_config = [
            'database_type' => $config['database_type'],
            'database_name' => $config['database_name'],
            'server' => $config['server'],
            'username' => $config['username'],
            'password' => $config['password'],
            'charset' => $config['charset']
        ];
        
        $this->log = new MyLog();

        $this->db = new Medoo($db_config);
    }

    public function handleConnect($data)
    {
        if(empty($data['uid'])){
            return false;
        }
        $database->insert('client_user', [
            'client_id' => $data['client_id'],
            'uid' => $data['uid']
        ]);
        $this->log(
            "uid:【" . $data['uid'] . "】登陆了，client_id:【" . $data['client_id'] . "】\n".
            "插入数据库【client_user】自增id：【" . $database->id() . "】"
        );
        //查找userinfo
        $userInfo = $database->get('user',[
            'nickname','id','avator'
        ],[
            'id'=>$data['uid']
        ]);
        $msg = [
            'msg_type' => 'connect',
            'userInfo' => $userInfo
        ];
        return $msg;
    }

    public function handleDemo($data)
    {
        //找到uid对应的client_id
        $msg_data = $data['msg'];
        $msg_data = array_column($msg_data,null,'uid');
        $uids = array_column($msg_data,'uid');
        $res = $database->select('client_user',['client_id','uid'],['uid' => $uids]);
        $client_ids = array_column($res,'client_id');
        $res = array_column($res,null,'client_id');
        foreach ($server->connections as $fd) {
            if (in_array($fd, $client_ids)) {
                if($data['client_id'] == $fd){
                    continue;
                }
                $msg = [
                    'msg' => $msg_data[$res[$fd]['uid']]['msg'],
                    'msg_type' => "message",
                    'send_time' => date("Y-m-d H:i:s")
                ];
                $server->push($fd, json_encode($msg));
            }
        }
    }

    public function handleMessage($data)
    {
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
    }

    public function handleGroupMessage($data)
    {
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
    }

    public function onMessage($data)
    {
        $msg_type = $data['msg_type'];
        if(!$msg_type) {
            return false;
        }
        $method = 'handle' . str_replace('_', '', $msg_type);
        return $this->$method($data);
    }

    public function add($table,$data) 
    {
        $this->db->insert($table, $data);
        return $this->db->id();
    }

    public function delete() 
    {

    }

    public function get() 
    {

    }

    public function log($log){
        $this->log->writeLog($log);
    }
}