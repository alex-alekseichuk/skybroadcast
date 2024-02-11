<?
//-- alter table services add service_price varchar(32) not null default '$0';
//-- update services set service_price='$0';

	mysql_connect("82.146.34.6", "skybroadcast", "skybroadcast") || die("Can't connect to database server");
	mysql_select_db("skybroadcast") || die("Can't select database");


mysql_query("alter table news add news_header_r varchar(50) null");
mysql_query("alter table news add news_body_r text null");
mysql_query("alter table news add news_header_a varchar(50) null");
mysql_query("alter table news add news_body_a text null");


mysql_query("alter table downloads add download_header_a  varchar(50) null");
mysql_query("alter table downloads add download_body_a text");
mysql_query("alter table downloads add download_header_r  varchar(50) null");
mysql_query("alter table downloads add download_body_r text");

mysql_query("alter table services add service_header_a varchar(50) null");
mysql_query("alter table services add service_desc_a varchar(250) null");
mysql_query("alter table services add service_header_r varchar(50) null");
mysql_query("alter table services add service_desc_r varchar(250) null");

mysql_query("alter table products add product_header_a varchar(50) null");
mysql_query("alter table products add product_desc_a varchar(250) null");
mysql_query("alter table products add product_header_r varchar(50) null");
mysql_query("alter table products add product_desc_r varchar(250) null");
	
mysql_query("alter table settings add setting_header_a varchar(50) null");
mysql_query("alter table settings add setting_desc_a varchar(250) null");
mysql_query("alter table settings add setting_header_r varchar(50) null");
mysql_query("alter table settings add setting_desc_r varchar(250) null");

mysql_query("create table users (login varchar(32), password varchar(32))");
mysql_query("insert into users (login,password) values ('admin', 'admin')");


mysql_query("create table strings (id varchar(32), en text, ar text, ru text)");
mysql_query("insert into strings (id, en, ar, ru) values ('home', 'insert home text here...', '', '')");


?>
