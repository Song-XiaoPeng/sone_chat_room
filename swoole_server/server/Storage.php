<?php
class Storage extends BaseStorage {
    //用户登陆，即当前所有在线信息，保存至redis client_id => uid
    public function login($msg){
        
    }

    //用户退出
    public function logout($client_id){

    }

    /* 群聊 */
    //用户发送群聊 group_id => client_id
    public function addGroupMember($group_id,$client_id) {

    }

    //用户退出群聊
    public function delGroupMember() {
        
    }

    //获得群聊历史记录
    public function getGroupHistory($group_id,$time = '') {
        
    }

    /*
     * 获得单聊历史记录 回话id page_size = 6 
     * 聊天历史可以保存至indexedDB，避免用户每次刷新都调后台接口
    */
    public function getHistory($session_id) {

    }

    //获得在线人员列表
    public function getOnLineList() {
    }

}