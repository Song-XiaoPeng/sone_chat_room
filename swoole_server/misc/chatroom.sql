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