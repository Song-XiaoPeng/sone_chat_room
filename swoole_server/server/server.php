<?php
use Medoo\Medoo;

class Server
{
    protected $server;
    protected $business;
    protected $log;
    protected $config;

    public function __construct($config)
    {
        //加载配置
        $this->config = $this->loadConfig();

        //加载日志处理对象
        $this->log = new MyLog();

        //注册自定义异常处理函数
        set_exception_handler([$this, 'exceptionHandler']);
        
        $this->server = new swoole_websocket_server($this->config['ws']['host'], $this->config['ws']['port']);

        $this->business = new Business($config);
    }

    public function onOpen(swoole_websocket_server $server, $request)
    {
        $log = "client_id: {$request->fd} 连接成功";
        $this->Log($log);

        $msg = [
            'client_id' => $request->fd,
            'msg_type' => 'onconnect'
        ];
        $server->push($request->fd,json_encode($msg));
    }

    public function onMessage(swoole_websocket_server $server, $frame)
    {
        $data = json_decode($frame->data, true);

        $data['client_id'] = $frame->fd;
        $msg = $this->business->onMessage($data);
        
        if($msg == false) {
            return false;
        }        
    }

    public function onClose($server, $fd)
    {
        $database->delete('client_user', ['client_id' => $fd]);
        echo "client {$fd} closed\n";
        echo "删除client_id:{$fd}, 受影响行数：" . $database->rowCount() . "数据库【client_user】\n";
    }

    protected function Log($log) 
    {
        $this->log->writeLog($log);
    }

    public function exceptionHandler($error){
        $this->log->writeLog($error);
    }

    protected function loadConfig()
    {
        return require_once(CONFIG_PATH . '/config.php');
    }
}

