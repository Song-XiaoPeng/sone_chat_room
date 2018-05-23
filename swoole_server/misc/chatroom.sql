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

# 单聊聊天记录表
create table single_chat_msg(
  id int unsigned primary key auto_increment default 0,
  session_id varchar(255) not null default '',
  send_uid int unsigned not null default 0,
  recv_uid int unsigned not null default 0,
  msg text,
  create_time default not null default '0000-00-00 00:00:00',
  key session_id(session_id),
  key send_uid(send_uid),
  key recv_uid(recv_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

# 群聊聊天记录表
create table single_chat_msg(
  id int unsigned primary key auto_increment default 0,
  group_id varchar(255) not null default '',
  send_uid int unsigned not null default 0,
  msg text,
  create_time default not null default '0000-00-00 00:00:00',
  key session_id(group_id),
  key send_uid(send_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

# 用户好友关系表
create table user_friends_relationship(
  id int unsigned primary key auto_increment default 0,
  uid int unsigned not null default 0,
  friend_uid int unsigned not null default 0,
  create_time default not null default '0000-00-00 00:00:00',
  key uid(uid),
  key friend_uid(friend_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

# 用户群聊关系表
create table user_group_relationship(
  id int unsigned primary key auto_increment default 0,
  group_id int unsigned not null default 0,
  uid int unsigned not null default 0,
  create_time default not null default '0000-00-00 00:00:00',
  key group_id(group_id),
  key uid(uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

# 群聊表
create table group(
  id int unsigned primary key auto_increment default 0,
  name varchar(255) not null default '',
  type int unsigned not null default 0,
  creator int  unsigned not null default 0 comment '创建者',
  desc varchar(255) not null default '' comment '群聊简介',
  member_total int unsigned not null default 0 comment '当前群聊人数',
  create_time default not null default '0000-00-00 00:00:00',
  update_time default not null default '0000-00-00 00:00:00',
  key creator(creator)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

# 标签表
create table tag(
  id int unsigned primary key auto_increment default 0,
  name varchar(255) not null default '',
  is_del tinyint not null default 0,
  create_time default not null default '0000-00-00 00:00:00',
  update_time default not null default '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

# 标签群聊关系表
create table tag_group_relationship(
  group_id int  unsigned not null default 0 comment '创建者',
  tag_id int  unsigned not null default 0 comment '创建者',
  key group_id(group_id),
  key tag_id(tag_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

