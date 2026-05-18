create database if not exists stray_rescue;
use stray_rescue;




create table if not exists email_verification(
    email_verification_id BINARY(16), 
    email_id varchar(100),
    table_name varchar(100) ,
    is_verified varchar(1) default 'N',
    
    
    primary key (email_verification_id)
);


create table if not exists Users(
	user_id BINARY(16) primary key,
    user_name varchar(100) not null,
    email varchar(100) not null Unique,
	password varchar(100) not null,
    joing_date timestamp default CURRENT_TIMESTAMP,
    user_profile_picture_link varchar(200) not null default "https://res.cloudinary.com/dvpwqtobj/image/upload/v1757076286/user_xhxvc9.png",
    user_bio text default ""
);


create table if not exists Employee(
	emp_id BINARY(16) primary key,
    emp_bio text default "",
    emp_name char(100) not null,
    emp_rank  int default 4,
    email varchar(100) not null Unique,
    password varchar(100) not null,
    salary decimal(16 , 8) default 10000,
    joing_date timestamp default CURRENT_TIMESTAMP,
    emp_profile_picture_link varchar(200) not null default 'https://res.cloudinary.com/dvpwqtobj/image/upload/v1757076286/user_xhxvc9.png',
    immediate_supervisor_id BINARY(16),
    rank_assign_date timestamp default CURRENT_TIMESTAMP,
    foreign key (immediate_supervisor_id) references Employee(emp_id)
);




create table if not exists rescue_point(
    rescue_point_name varchar(200) not null Unique, 
	rescue_point_id BINARY(16) primary key,
    rescue_point_location_latitude decimal(16,8) ,
    rescue_point_location_longtitude decimal(16,8),
    supervisor_id BINARY(16) ,
    creation_date timestamp default CURRENT_TIMESTAMP,
    foreign key (supervisor_id) references Employee(emp_id)
);


create table if not exists rescue_point_images(
    rescue_point_id BINARY(16),
    image_link   varchar(200) not null ,
    foreign key(rescue_point_id)references rescue_point(rescue_point_id),
    primary key(rescue_point_id , image_link)
);

alter table Employee add column if not exists rescue_point_id BINARY(16) references rescue_point(rescue_point_id);

create table if not exists rescue_post(
	rescue_post_id BINARY(16) primary key,
    rescue_post_image_link varchar(200) default null,
    rescue_post text ,
    animal_species_type varchar(100) default null,
    animal_gender_type char(1) default null,
    animal_age double default null,
    post_loc_latitude decimal(16 , 8) default null,
    post_loc_longtitude decimal(16 , 8) default null,
    post_time_stamp timestamp default CURRENT_TIMESTAMP,
	user_id BINARY(16),
    sos_level int default 1,
    foreign key(user_id) references Users(user_id)
    
);


create table if not exists volunteers(
    volunteer_id BINARY(16) primary key,
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
    rescue_post_id BINARY(16),
    volunteer_id BINARY(16),
    FOREIGN KEY (rescue_post_id)
        REFERENCES rescue_post (rescue_post_id),
    FOREIGN KEY (volunteer_id)
        REFERENCES volunteers (volunteer_id),
    PRIMARY KEY (rescue_post_id , volunteer_id)
);


CREATE TABLE IF NOT EXISTS animals (
    animal_id BINARY(16) PRIMARY KEY,
    rescue_point_id Binary(16),
    species_type VARCHAR(100) NOT NULL,
    gender_type CHAR(1) DEFAULT NULL,
    age DOUBLE DEFAULT NULL,
    health_status TEXT DEFAULT NULL,
    activity_level VARCHAR(50) DEFAULT NULL,

    foreign key (rescue_point_id) references rescue_point(rescue_point_id)
);


CREATE TABLE IF NOT EXISTS adoption_post (
    adoption_post_id BINARY(16) PRIMARY KEY ,
    animal_id BINARY(16) NOT NULL,
    adoption_post_image_count INT NOT NULL DEFAULT 1, 
    adoption_post_image_link VARCHAR(1000) DEFAULT NULL,
    adoption_post_text TEXT,
    post_loc_latitude DECIMAL(16,8) DEFAULT NULL,
    post_loc_longitude DECIMAL(16,8) DEFAULT NULL,
    post_time_stamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    emp_id BINARY(16) DEFAULT NULL,
    rescue_point_id BINARY(16) DEFAULT NULL,
    user_id BINARY(16) DEFAULT NULL,
    
    FOREIGN KEY (animal_id) REFERENCES animals(animal_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (emp_id) REFERENCES Employee(emp_id),
    FOREIGN KEY (rescue_point_id) REFERENCES rescue_point(rescue_point_id)
);


CREATE TABLE IF NOT EXISTS adoption_queue (
    queue_id BINARY(16) PRIMARY KEY,
    adoption_post_id BINARY(16) NOT NULL,
    user_id BINARY(16) NOT NULL,
    request_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    
    FOREIGN KEY (adoption_post_id) REFERENCES adoption_post(adoption_post_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

create table if not exists notifications(
    notification_id BINARY(16) PRIMARY KEY,
    user_id BINARY(16) NOT NULL,
    user_type char(100) not null , -- Users , Employee , volunteers
    message TEXT NOT NULL,
    related_post_id BINARY(16) DEFAULT NULL,
    notification_type ENUM('rescue','adoption','queue','general') DEFAULT 'general',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

create table if not exists like_registry_adoption_post(
    adoption_post_id BINARY(16),
    user_id BINARY(16),
    like_type int default 1,
    foreign key (adoption_post_id) references adoption_post(adoption_post_id),
    foreign key (user_id) references Users(user_id) ,
    primary key(adoption_post_id , like_type)
);



create table if not exists comment_registry_adoption_post(
    comment_id BINARY(16) primary key,
    adoption_post_id BINARY(16) ,
    user_id BINARY(16),
    comment_text text,
    replying_to BINARY(16) default null,
    foreign key (adoption_post_id) references adoption_post(adoption_post_id),
    foreign key (user_id) references Users(user_id) ,
    foreign key (replying_to) references comment_registry_adoption_post(comment_id)
);




-- Triggers
use stray_rescue;



CREATE TRIGGER if not exists before_insert_users
BEFORE INSERT ON Users
FOR EACH ROW
SET NEW.user_id = UNHEX(REPLACE(UUID(), '-', ''));

CREATE TRIGGER if not exists before_insert_employee
BEFORE INSERT ON Employee
FOR EACH ROW
SET NEW.emp_id = UNHEX(REPLACE(UUID(), '-', ''));


CREATE TRIGGER if not exists after_update_employee
BEFORE UPDATE ON Employee
FOR EACH ROW
    BEGIN
        IF OLD.emp_rank <> NEW.emp_rank THEN
            SET NEW.rank_assign_date = CURRENT_TIMESTAMP;
        END IF;
END;



CREATE TRIGGER if not exists before_insert_volunteers
BEFORE INSERT ON volunteers
FOR EACH ROW
SET NEW.volunteer_id = UNHEX(REPLACE(UUID(), '-', ''));

CREATE TRIGGER if not exists before_insert_rescue_point
BEFORE INSERT ON rescue_point
FOR EACH ROW
SET NEW.rescue_point_id = UNHEX(REPLACE(UUID(), '-', ''));



CREATE TRIGGER if not exists before_insert_animals
BEFORE INSERT ON animals
FOR EACH ROW
SET NEW.animal_id = UNHEX(REPLACE(UUID(), '-', ''));



CREATE TRIGGER if not exists before_insert_adoption_queue
BEFORE INSERT ON adoption_queue
FOR EACH ROW
SET NEW.queue_id = UNHEX(REPLACE(UUID(), '-', ''));

CREATE TRIGGER if not exists before_insert_notifications
BEFORE INSERT ON notifications
FOR EACH ROW
SET NEW.notification_id = UNHEX(REPLACE(UUID(), '-', ''));

CREATE TRIGGER if not exists before_insert_comment_registry_adoption_post
BEFORE INSERT ON comment_registry_adoption_post
FOR EACH ROW
SET NEW.comment_id = UNHEX(REPLACE(UUID(), '-', ''));



-- indexes for tables
-- Employee
CREATE INDEX if not exists idx_emp_rank ON Employee(emp_rank);
CREATE INDEX if not exists  idx_employee_name ON Employee(emp_name);

CREATE INDEX if not exists idx_rescue_point_geo ON rescue_point(
    rescue_point_location_latitude,
    rescue_point_location_longtitude
);

CREATE INDEX if not exists  idx_rescue_post_sos ON rescue_post(sos_level);

CREATE INDEX if not exists  idx_animal_species ON animals(species_type);

CREATE INDEX  if not exists idx_rescue_point_name 
ON rescue_point(rescue_point_name);


-- testing sql queries


insert ignore into email_verification(email_verification_id , email_id , table_name , is_verified)
values('a' , 'a' , 'Users' ,'Y');

insert ignore into email_verification(email_verification_id , email_id , table_name , is_verified)
values('b' , 'b' , 'Employee' ,'Y');

insert ignore into email_verification(email_verification_id , email_id , table_name , is_verified)
values ('c' , 'c' , 'volunteers' , "Y");


insert ignore into Users(  user_name , email , password) values(
    'a' , 'a' , 'a'
);

insert ignore into Employee(emp_name , email , password , emp_rank ) values(
    'b' ,'b' ,'b' , 0
);

insert ignore into Employee(emp_name , email , password , emp_rank ) values(
    'b1' ,'b1' ,'b1' , 4
);

insert ignore into Employee(emp_name , email , password , emp_rank ) values(
    'b2' ,'b2' ,'b2' , 3
);

insert ignore into Employee(emp_name , email , password , emp_rank ) values(
    'b3' ,'b3' ,'b3' , 2
);

insert ignore into volunteers(volunteer_name , email , password ) values(
    'c' ,'c' ,'c'
);



-- functions


-- levenstein string distance function for finding distance between two strings


-- DELIMITER $$

-- CREATE FUNCTION levenshtein(s1 VARCHAR(255), s2 VARCHAR(255))
-- RETURNS INT
-- DETERMINISTIC
-- BEGIN
--     DECLARE s1_len, s2_len, i, j, cost INT;
--     DECLARE lastdiag, olddiag INT;
--     DECLARE s1_char CHAR;
--     DECLARE cv0 VARBINARY(256);
--     DECLARE cv1 VARBINARY(256);

--     SET s1_len = CHAR_LENGTH(s1);
--     SET s2_len = CHAR_LENGTH(s2);

--     IF s1 = s2 THEN
--         RETURN 0;
--     END IF;

--     IF s1_len = 0 THEN
--         RETURN s2_len;
--     END IF;

--     IF s2_len = 0 THEN
--         RETURN s1_len;
--     END IF;

--     SET j = 0;
--     SET cv1 = '';

--     WHILE j <= s2_len DO
--         SET cv1 = CONCAT(cv1, CHAR(j));
--         SET j = j + 1;
--     END WHILE;

--     SET i = 1;

--     WHILE i <= s1_len DO
--         SET s1_char = SUBSTRING(s1, i, 1);
--         SET cv0 = CHAR(i);
--         SET lastdiag = i - 1;

--         SET j = 1;

--         WHILE j <= s2_len DO
--             IF s1_char = SUBSTRING(s2, j, 1) THEN
--                 SET cost = 0;
--             ELSE
--                 SET cost = 1;
--             END IF;

--             SET olddiag = ORD(SUBSTRING(cv1, j, 1));

--             SET lastdiag = LEAST(
--                 ORD(SUBSTRING(cv1, j + 1, 1)) + 1,
--                 ORD(SUBSTRING(cv0, j, 1)) + 1,
--                 olddiag + cost
--             );

--             SET cv0 = CONCAT(cv0, CHAR(lastdiag));
--             SET j = j + 1;
--         END WHILE;

--         SET cv1 = cv0;
--         SET i = i + 1;
--     END WHILE;

--     RETURN lastdiag;
-- END$$

-- DELIMITER ;