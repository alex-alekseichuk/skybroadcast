-- alter table services add service_price varchar(32) not null default '$0';
-- update services set service_price='$0';


alter table news add news_header_r varchar(50) null;
alter table news add news_body_r text null;
alter table news add news_header_a varchar(50) null;
alter table news add news_body_a text null;


alter table downloads add download_header_a  varchar(50) null;
alter table downloads add download_body_a text;
alter table downloads add download_header_r  varchar(50) null;
alter table downloads add download_body_r text;

alter table services add service_header_a varchar(50) null;
alter table services add service_desc_a varchar(250) null;
alter table services add service_header_r varchar(50) null;
alter table services add service_desc_r varchar(250) null;

alter table products add product_header_a varchar(50) null;
alter table products add product_desc_a varchar(250) null;
alter table products add product_header_r varchar(50) null;
alter table products add product_desc_r varchar(250) null;
	
alter table settings add setting_header_a varchar(50) null;
alter table settings add setting_desc_a varchar(250) null;
alter table settings add setting_header_r varchar(50) null;
alter table settings add setting_desc_r varchar(250) null;

