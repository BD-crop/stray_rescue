create database if not exists stray_rescue;
use stray_rescue;

create table if not exists rescue_point(
	rescue_point_id varchar(36) default UUID() primary key,
    rescue_point_location_latitude decimal(16,8) ,
    rescue_point_location_longtitude decimal(16,8)
);


create table if not exists Users(
	user_id varchar(36) default UUID() primary key,
    user_name varchar(100) not null,
    email varchar(100) not null Unique,
	password varchar(100) not null,
    user_profile_picture_link varchar(200) not null
);


create table if not exists Employee(
	emp_id varchar(36) default UUID() primary key,
    emp_name varchar(100) not null,
    email varchar(100) not null Unique,
    password varchar(100) not null,
    rescue_point_id varchar(36) default null ,
    super_visor_id varchar(36) default null,
    salary decimal(16 , 8) default null,
    emp_profile_picture_link varchar(200) not null,
    foreign key (rescue_point_id) references rescue_point(rescue_point_id),
    foreign key (super_visor_id) references Employee(emp_id)
);

create table if not exists rescue_post(
	rescue_post_id varchar(36) default UUID() primary key,
    rescue_post_image_link varchar(200) default null,
    rescue_post text ,
    animal_species_type varchar(100) default null,
    animal_gender_type char(1) default null,
    animal_age double default null,
    animal_species_age double default null,	
    post_loc_latitude decimal(16 , 8) default null,
    post_loc_longtitude decimal(16 , 8) default null,
    post_time_stamp timestamp default CURRENT_TIMESTAMP,
	user_id varchar(36),
    
    foreign key(user_id) references Users(user_id)
    
);



create table if not exists volunteers(
	volunteer_id varchar(36) default UUID() primary key,
    email varchar(100) not null Unique,
    password varchar(100) not null,
	volunteer_image_link varchar(200),
    volunteer_name  varchar(100),
    volunteer_location_latitude decimal(16 , 8) default null,
    volunteer_location_longtitude decimal (16 , 8) default null
);

CREATE TABLE IF NOT EXISTS rescued_event (
    rescue_post_id VARCHAR(36),
    volunteer_id VARCHAR(36),
    FOREIGN KEY (rescue_post_id)
        REFERENCES rescue_post (rescue_post_id),
    FOREIGN KEY (volunteer_id)
        REFERENCES volunteers (volunteer_id),
    PRIMARY KEY (rescue_post_id , volunteer_id)
);


create table if not exists adoption_post(
	adoption_post_id varchar(36) default UUID() primary key,
    adoption_post_image_link varchar(200) default null,
    adoption_post_text text ,
    animal_species_type varchar(100) default null,
    animal_gender_type char(1) default null,
    animal_age double default null,
    animal_species_age double default null,	
    post_loc_latitude decimal(16 , 8) default null,
    post_loc_longtitude decimal(16 , 8) default null,
    post_time_stamp timestamp default CURRENT_TIMESTAMP,
	emp_id varchar(36),
    rescue_point_id varchar(36) ,
    user_id varchar(36) ,
    
    foreign key(user_id) references Users(user_id),
	foreign key(emp_id) references Employee(emp_id),
    foreign key(rescue_point_id) references rescue_point(rescue_point_id)
);

create table if not exists like_registry_adoption_post(
    adoption_post_id varchar(36) ,
    user_id varchar(36),
    like_type int default 1,
    foreign key (adoption_post_id) references adoption_post(adoption_post_id),
    foreign key (user_id) references Users(user_id) ,
    primary key(adoption_post_id , like_type)
);


create table if not exists comment_registry_adoption_post(
    comment_id varchar(36) default UUID() primary key,
    adoption_post_id varchar(36) ,
    user_id varchar(36),
    comment_text text,
    replying_to varchar(36) default null,
    foreign key (adoption_post_id) references adoption_post(adoption_post_id),
    foreign key (user_id) references Users(user_id) ,
    foreign key (replying_to) references comment_registry_adoption_post(comment_id)

);
