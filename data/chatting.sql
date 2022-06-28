create database chatting;

use chatting;

create table users(
	id int unsigned auto_increment not null primary key,
	username char(50) not null,
	hash char(255) not null,
	email char(100) not null,
	
	unique index(email),
	unique index(username)
);

/*grant select,insert,update,delete
on chatting.*
to php;*/

create table relations(
	id int unsigned auto_increment not null primary key,
	name1 char(50) not null,
	name2 char(50) not null,
	alias char(50) not null,  /*alias for name2*,set by name1*/
	last_query int unsigned not null default(0), /*name1上次查询来自于name2消息的时间*/
	last_send int unsigned not null default(0),	/*name1上次给name2发送消息的时间，name1每次给name2发送消息这个值都会更新*/
	
	index(name1,name2),
	index(name2),
	
	foreign key (name1) references users(username) on delete cascade,
	foreign key (name2) references users(username) on delete cascade
);

/*grant select,insert,update,delete
on relations.*
to php;*/

create table records(
	id int unsigned auto_increment not null primary key,
	message text not null,
	sent_by char(50) not null,
	sent_to char(50) not null,
	date_created int unsigned not null,
	
	index (date_created DESC,sent_to,sent_by),
	index (sent_to,sent_by),
	foreign key (sent_by) references users(username) on delete cascade,
	foreign key (sent_to) references users(username) on delete cascade
);

/*grant select,insert,update,delete
on records.*
to php;*/

create table request_contact( /*一方提出添加联系人请求后要得到另一方的同意才行，这个表保存这种请求，在用户登陆时搜索这个表，向用户展示新增的请求*/
	id int unsigned auto_increment not null primary key,
	sent_by char(50) not null,
	sent_to char(50) not null,
	alias char(50) not null, 
	done bool not null default(false),  /*每一次取走请求都要修改一次表，把done设置为true，但这种request_contact请求数量远远少于表recodes的行数，效率略低也可接受*/
	
	index (sent_to,sent_by),
	foreign key (sent_by) references users(username) on delete cascade,
	foreign key (sent_to) references users(username) on delete cascade
);

grant select,insert,update,delete
on chatting.*
to 'php'@'localhost';