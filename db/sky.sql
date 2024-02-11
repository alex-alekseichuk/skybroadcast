DROP TABLE IF EXISTS news;
CREATE TABLE news (
	news_id int unsigned not null primary key auto_increment,
	news_date  varchar(50) null,
	news_header varchar(50) null,
	news_body text,

	news_header_a varchar(50) null,
	news_body_a text,
	news_header_r varchar(50) null,
	news_body_r text
);



DROP TABLE IF EXISTS downloads;
CREATE TABLE downloads (
	download_id int unsigned not null primary key auto_increment,
	download_header  varchar(50) null,
	download_url varchar(250) null,
	download_body text,

	download_header_a  varchar(50) null,
	download_body_a text,
	download_header_r  varchar(50) null,
	download_body_r text
);



DROP TABLE IF EXISTS services;
CREATE TABLE services (
	service_id int unsigned not null primary key auto_increment,
	service_header varchar(50) null,
	service_desc varchar(250) null,

	service_header_a varchar(50) null,
	service_desc_a varchar(250) null,
	service_header_r varchar(50) null,
	service_desc_r varchar(250) null,

	service_price decimal(9,2) not null default 0.0
);



DROP TABLE IF EXISTS products;
CREATE TABLE products (
	product_id int unsigned not null primary key auto_increment,
	product_header varchar(50) null,
	product_desc varchar(250) null,

	product_header_a varchar(50) null,
	product_desc_a varchar(250) null,
	product_header_r varchar(50) null,
	product_desc_r varchar(250) null
);




DROP TABLE IF EXISTS settings;
CREATE TABLE settings (
	setting_id int unsigned not null primary key auto_increment,
	setting_header varchar(50) null,
	setting_desc varchar(250) null,

	setting_header_a varchar(50) null,
	setting_desc_a varchar(250) null,
	setting_header_r varchar(50) null,
	setting_desc_r varchar(250) null
);


DROP TABLE IF EXISTS users;
create table users (login varchar(32), password varchar(32));
insert into users (login,password) values ('admin', 'admin');


DROP TABLE IF EXISTS strings;
create table strings (id varchar(32), en text, ar text, ru text);
insert into strings (id, en, ar, ru) values ('home', 'insert home text here...', '', '');

