# sone_chat_room
基于vue2.0+Swoole开发的web聊天室

## 项目介绍
1. 前端界面使用vue开发，同时使用了移动端UI vux
2. 服务端使用`PHP的异步、并行、高性能网络通信引擎` `Swoole扩展`

## 版本说明
1. 版本号
	- 1.0.0 主版本号.次版本号.修改版本号
	- 稳定版本发布修改`主版本号`
	- 重要功能增加修改`次版本号`
	- 功能修改，调试修改`修改版本号`

### vue客户端
1. 进入项目目录，执行

``` 
npm install
npm run dev

浏览器输入 localhost:8080 即可运行
```

### swoole服务端
1. linux安装swoole扩展
2. 进入项目目录，执行`php server.php` 
3. 默认已开启了守护进程模式

## 目录结构

```
vue_client  vue客户端
  
	- src
		
		-static
			- image 该文件夹为网站静态资源存放路径，设计图标放置于该文件下，可以在该文件夹下新建文件夹


swoole_server swoole  服务端

```
## 版权声明