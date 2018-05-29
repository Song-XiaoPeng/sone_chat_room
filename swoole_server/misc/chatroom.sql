create table client_user (
  uid int not null default 0,
  client_id int not null default 0,
  key client_user_uid (uid) ,
  key client_user_client_id (client_id)
)

create table chat_message (
  uid int unsigned not null default 0,
  sendTo int unsigned not null default 0,
  msg varchar(255) not null default '',
  createtime int unsigned not null default 0,
  key client_user_uid (uid) ,
  key client_user_sendto (sendTo)
)

# v1.1

create database sone_chat_room charset=utf8mb4 COLLATE utf8mb4_general_ci

# 用户表
create table u_user (
  id int unsigned primary key auto_increment,
  username varchar(40) not null DEFAULT '' COMMENT '登陆用户名',
  `password` varchar(255) DEFAULT NULL COMMENT '密码',
  `phone` varchar(20) DEFAULT NULL COMMENT '手机号码',
  `email` varchar(255) DEFAULT NULL COMMENT '邮箱',
  `is_forbid` tinyint(1) unsigned DEFAULT '0' COMMENT '是否禁用',
  `remark` varchar(200) DEFAULT NULL COMMENT '备注',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间【created_time】',
  `updated_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间【updated_time】',
  `last_ip` varchar(30) DEFAULT NULL COMMENT '上次登录ip',
  unique phone (phone),
  unique email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

# 用户其它信息表
create table u_user_extra_info (
  id int unsigned primary key auto_increment,
  uid int unsigned not null default 0 comment '用户uid',
  nickname varchar(40) not null DEFAULT '' COMMENT '昵称',
  `personal_signature` varchar(2000) DEFAULT NULL COMMENT '个性签名',
  `avator` varchar(255) DEFAULT NULL COMMENT '头像',
  `avator_id` int not null default 0 comment '头像文件表主键id',
  realname varchar(40) not null DEFAULT '' COMMENT '真实名称',
  address varchar(255) not null DEFAULT '' COMMENT '所在地址',
  is_show tinyint not null default 0 comment '是否显示个人隐私 0否1是',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间【created_time】',
  `updated_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间【updated_time】',
  unique uid (uid),
  key avator_id (uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

# 文件表
create table a_attachment (
  id int unsigned primary key auto_increment,
  uid int unsigned not null default 0 comment '用户uid',
  type int unsigned not null default 0 comment '文件分类0头像',
  filename varchar(40) not null DEFAULT '' COMMENT '文件名称',
  original_name varchar(40) not null DEFAULT '' COMMENT '文件原名称',
  `prefix` varchar(255) DEFAULT NULL COMMENT '文件路径前缀',
  `full_path` varchar(255) DEFAULT NULL COMMENT '文件全路径',
  mime_type varchar(40) not null DEFAULT '' COMMENT '文件真实类型',
  is_del tinyint not null default 0 comment '是否显示个人信息realname address 0否1是',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间【created_time】',
  `updated_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间【updated_time】',
  key uid (uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

# 用户标签表
create table u_user_tag (
  id int unsigned primary key auto_increment,
  uid int unsigned not null default 0 comment '用户uid',
  tag_name varchar(40) not null DEFAULT '' COMMENT '标签名称',
  count int unsigned not null default 0 comment '标签出现次数',
  is_del tinyint not null default 0 comment '是否删除0否1是',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间【created_time】',
  `updated_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间【updated_time】',
  key uid (uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

# 单聊会话表
create table s_single_chat_session(
  id int unsigned primary key auto_increment,
  send_uid int unsigned not null default 0 comment '发送者uid',
  recv_uid int unsigned not null default 0 comment '接收者uid',
  is_del tinyint unsigned not null default 0 comment '是否删除',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间【created_time】',
  `updated_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间【updated_time】',
  key send_uid(send_uid),
  key recv_uid(recv_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

# 单聊聊天记录表
create table s_single_chat_msg(
  id int unsigned primary key auto_increment,
  session_id int unsigned not null default 0 comment '会话表主键id',
  send_uid int unsigned not null default 0 comment '发送者uid',
  recv_uid int unsigned not null default 0 comment '接收者uid',
  msg text  comment '聊天记录',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间【created_time】',
  `updated_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间【updated_time】',
  key session_id(session_id),
  key send_uid(send_uid),
  key recv_uid(recv_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

# 群聊聊天记录表
create table g_group_chat_msg(
  id int unsigned primary key auto_increment,
  group_id int unsigned not null default 0 comment '群聊表主键id',
  send_uid int unsigned not null default 0 comment '发送者uid',
  msg text comment '聊天记录',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间【created_time】',
  `updated_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间【updated_time】',
  key session_id(group_id),
  key send_uid(send_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

# 用户好友关系表
create table u_user_friends_relationship(
  id int unsigned primary key auto_increment,
  uid int unsigned not null default 0 comment 'uid',
  friend_uid int unsigned not null default 0 comment '好友uid',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间【created_time】',
  `updated_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间【updated_time】',
  key uid(uid),
  key friend_uid(friend_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

# 用户群聊关系表
create table u_user_group_relationship(
  id int unsigned primary key auto_increment,
  group_id int unsigned not null default 0 comment '群聊id',
  uid int unsigned not null default 0 comment '群聊包含用户uid',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间【created_time】',
  `updated_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间【updated_time】',
  key group_id(group_id),
  key uid(uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

# 群聊表
create table g_group(
  id int unsigned primary key auto_increment,
  name varchar(255) not null default '' comment '群聊名称',
  creator int  unsigned not null default 0 comment '创建者',
  group_desc varchar(255) not null default '' comment '群聊简介',
  member_total int unsigned not null default 0 comment '当前群聊人数',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间【created_time】',
  `updated_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间【updated_time】',
  key creator(creator)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

# 群聊标签表
create table t_tag(
  id int unsigned primary key auto_increment,
  name varchar(255) not null default '' comment '群聊标签名称',
  is_del tinyint unsigned not null default 0 comment '是否删除',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间【created_time】',
  `updated_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间【updated_time】'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

# 标签群聊关系表
create table t_tag_group_relationship(
  group_id int  unsigned not null default 0 comment '群聊id',
  tag_id int  unsigned not null default 0 comment '标签id',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间【created_time】',
  `updated_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间【updated_time】',
  key group_id(group_id),
  key tag_id(tag_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

# 登陆记录表
create table l_login_log(
  id int unsigned primary key auto_increment,
  uid int unsigned not null default 0 comment '登陆用户uid',
  log varchar(255) not null default '' comment '日志记录',
  ip varchar(50) not null default '' comment '登陆ip',
  ip_district varchar(50) not null default '' comment 'IP所在地区',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间【created_time】',
  `updated_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间【updated_time】',
  key uid(uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;


