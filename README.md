# sone_chat_room
> 基于vue2.0+Swoole开发的web聊天室

[在线体验地址](http://sone.timeline.hellobirds.top)

## 项目介绍
1. 界面使用vue2.0、移动端UI vux
2. 服务端使用`PHP的异步、并行、高性能网络通信引擎` `Swoole扩展`
3. 功能
    - 群聊分组
    - 好友列表
    - 兴趣中心
    - 个人中心
4. 技术栈
    - Vue 
    - Swoole 
    - WebSocket
    - Mysql 
    - Redis 
    - IndexedDB
    - Vux

### 目的
1. 旨在开发的过程中，不断完善自己的技能，打磨自己的技术，培养前后端的架构能力，以及美感
2. 按照框架的规范，来严格要求自己的代码质量，同时不断美化界面，优化用户交互，提升用户体验
3. 不只是开发，更希望作品能够被身边的人使用。会朝着这个方向不断迈进
4. 欢迎感兴趣的童鞋一起开发，一起玩

## 版本说明
1. 版本号
	- 1.0.0 主版本号.次版本号.修改版本号
	- 稳定版本发布修改`主版本号`
	- 重要功能增加修改`次版本号`
	- 功能修改，调试修改`修改版本号`

## 安装和运行
```
git clone https://github.com/Song-XiaoPeng/sone_chat_room.git
```

### 客户端运行
``` 
cd vue_client

npm install

npm run dev     //浏览器访问 http://localhost:8080

npm run build   //项目打包

```

### swoole服务端
1. linux安装swoole扩展
2. 进入项目目录，执行`php server.php` 
3. 默认已开启了守护进程模式

## 目录结构
```
.
├── vue_client                  vue客户端
|   ├── build 
|   ├── config 
|   ├── dist 
|   ├── src                     前台核心目录
|       ├── api                 api接口、工具类
|       ├── assets 
|       ├── components          视图、组件
|       ├── router              路由
|       ├── App.vue 
|       ├── main.js             入口文件
|   ├── static 
├── swoole_server swoole        服务端
|   ├── config                  php配置文件
|   ├── home 
|   ├── misc                    sql文件、nginx配置
|   ├── runtime                 运行日志
|   ├── server                  核心服务
|       ├── Business.php        业务逻辑代码
|       ├── MyException.php     异常类
|       ├── MyLog.php           日志处理类
|       ├── server.php          核心服务（面向过程）
|       ├── Swoole.php          核心服务（面向对象）
├── vendor                      composer依赖文件
├── composer.json
├── README.md
├── LICENSE
```
## 版权声明