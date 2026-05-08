create database if not exists stray_rescue;
use stray_rescue;




create table if not exists email_verification(
    email_verification_id varchar(36) default UUID(), 
    email_id varchar(100),
    table_name varchar(100) ,
    is_verified varchar(1) default 'N',

    
    primary key(email_id , table_name)
);


create table if not exists Users(
	user_id varchar(36) default UUID() primary key,
    user_name varchar(100) not null,
    email varchar(100) not null Unique,
	password varchar(100) not null,
    joing_date timestamp default CURRENT_TIMESTAMP,
    user_profile_picture_link varchar(200) not null default "https://res.cloudinary.com/dvpwqtobj/image/upload/v1757076286/user_xhxvc9.png",
    user_bio text default ""
);


create table if not exists Employee(
	emp_id varchar(36) default UUID() primary key,
    emp_bio text default "",
    emp_name varchar(100) not null,
    emp_rank  int default 5,
    email varchar(100) not null Unique,
    password varchar(100) not null,
    salary decimal(16 , 8) default null,
    joing_date timestamp default CURRENT_TIMESTAMP,
    emp_profile_picture_link varchar(200) not null default 'https://res.cloudinary.com/dvpwqtobj/image/upload/v1757076286/user_xhxvc9.png',
);

create table if not exists rescue_point(

	rescue_point_id varchar(36) default UUID() primary key,
    rescue_point_location_latitude decimal(16,8) ,
    rescue_point_location_longtitude decimal(16,8),
    supervisor_id varchar(36) ,
    foreign key (supervisor_id) references Employee(emp_id),

);
alter table Employee add column rescue_point_id varchar(36) references rescue_point(rescue_point_id);

create table if not exists rescue_post(
	rescue_post_id varchar(36) default UUID() primary key,
    rescue_post_image_link varchar(200) default null,
    rescue_post text ,
    animal_species_type varchar(100) default null,
    animal_gender_type char(1) default null,
    animal_age double default null,
    post_loc_latitude decimal(16 , 8) default null,
    post_loc_longtitude decimal(16 , 8) default null,
    post_time_stamp timestamp default CURRENT_TIMESTAMP,
	user_id varchar(36),
    sos_level int default 1,
    foreign key(user_id) references Users(user_id)
    
);



create table if not exists volunteers(
	volunteer_id varchar(36) default UUID() primary key,
    volunteer_bio text default "",
    email varchar(100) not null Unique,
    password varchar(100) not null,
	volunteer_image_link varchar(200) default "https://res.cloudinary.com/dvpwqtobj/image/upload/v1757076286/user_xhxvc9.png",
    volunteer_name  varchar(100),
    joing_date timestamp default CURRENT_TIMESTAMP,
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


CREATE TABLE IF NOT EXISTS animals (
    animal_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    species_type VARCHAR(100) NOT NULL,
    gender_type CHAR(1) DEFAULT NULL,
    age DOUBLE DEFAULT NULL,
    health_status TEXT DEFAULT NULL,
    activity_level VARCHAR(50) DEFAULT NULL
);


CREATE TABLE IF NOT EXISTS adoption_post (
    adoption_post_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    animal_id CHAR(36) NOT NULL,
    adoption_post_image_count INT NOT NULL DEFAULT 1, 
    adoption_post_image_link VARCHAR(1000) DEFAULT NULL,
    adoption_post_text TEXT,
    post_loc_latitude DECIMAL(16,8) DEFAULT NULL,
    post_loc_longitude DECIMAL(16,8) DEFAULT NULL,
    post_time_stamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    emp_id CHAR(36) DEFAULT NULL,
    rescue_point_id CHAR(36) DEFAULT NULL,
    user_id CHAR(36) DEFAULT NULL,
    
    FOREIGN KEY (animal_id) REFERENCES animals(animal_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (emp_id) REFERENCES Employee(emp_id),
    FOREIGN KEY (rescue_point_id) REFERENCES rescue_point(rescue_point_id)
);


CREATE TABLE IF NOT EXISTS adoption_queue (
    queue_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    adoption_post_id CHAR(36) NOT NULL,
    user_id CHAR(36) NOT NULL,
    request_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    
    FOREIGN KEY (adoption_post_id) REFERENCES adoption_post(adoption_post_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

create table if not exists notifications(){
    notification_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    user_id CHAR(36) NOT NULL,
    user_type char(100) not null , // Users , Employee , volunteers
    message TEXT NOT NULL,
    related_post_id CHAR(36) DEFAULT NULL,
    notification_type ENUM('rescue','adoption','queue','general') DEFAULT 'general',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
}

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
