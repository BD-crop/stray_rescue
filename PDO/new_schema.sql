create database if not exists stray_rescue;

use stray_rescue;


-- Rescue Shelter logic 

create table if not exists rescue_point (
        rescue_point_name varchar(200) not null Unique,
        rescue_point_id CHAR(36) primary key,
        rescue_point_location_latitude decimal(16, 8),
        rescue_point_location_longtitude decimal(16, 8),
        supervisor_id CHAR(36) default NULL,
        creation_date timestamp default CURRENT_TIMESTAMP,
        is_closed int default 0 
);

create table if not exists rescue_point_images (
        rescue_point_id CHAR(36) default NULL,
        image_link varchar(200) not null,
        foreign key (rescue_point_id) references rescue_point (rescue_point_id),
        primary key (rescue_point_id, image_link)
);



-- Email verification logic (sign in / sign up)


create table if not exists email_verification (
        email_verification_id CHAR(36),
        email_id varchar(100),
        table_name varchar(100),
        is_verified varchar(1) default 'N',
        primary key (email_verification_id)
);

create table if not exists Users (
        user_id CHAR(36) primary key,
        user_name varchar(100) not null,
        email varchar(100) not null Unique,
        password varchar(100) not null,
        joing_date timestamp default CURRENT_TIMESTAMP,
        user_profile_picture_link varchar(200) not null default "https://res.cloudinary.com/dvpwqtobj/image/upload/v1757076286/user_xhxvc9.png",
        user_bio text default "",
        is_deleted int default 0
);

create table if not exists Employee (
        emp_id CHAR(36) primary key default UUID(),
        emp_bio text default "",
        emp_name char(100) not null,
        emp_rank int default 4,
        check (emp_rank BETWEEN  0 AND 4),
        email varchar(100) not null Unique,
        password varchar(100) not null,
        salary decimal(16, 8) default 10000,
        joing_date timestamp default CURRENT_TIMESTAMP,
        emp_profile_picture_link varchar(200) not null default 'https://res.cloudinary.com/dvpwqtobj/image/upload/v1757076286/user_xhxvc9.png',
        immediate_supervisor_id CHAR(36) default NULL,
        foreign key (immediate_supervisor_id) references Employee (emp_id),
        rescue_point_id CHAR(36) default NULL,
        has_resigned int default 0
);






create table if not exists volunteers (
        volunteer_id CHAR(36) primary key,
        volunteer_bio text default "",
        email varchar(100) not null Unique,
        password varchar(100) not null,
        volunteer_image_link varchar(200) default "https://res.cloudinary.com/dvpwqtobj/image/upload/v1757076286/user_xhxvc9.png",
        volunteer_name varchar(100),
        joing_date timestamp default CURRENT_TIMESTAMP,
        volunteer_location_latitude decimal(16, 8) default null,
        volunteer_location_longtitude decimal(16, 8) default null,
        has_resigned int default 0
);


-- Rescue Post (Here is just mean the registered animals , community tagged animals)

create table if not exists rescue_post (
        rescue_post_id CHAR(36)  primary key,
        rescue_post text,
        animal_species_type varchar(100) default null,
        animal_gender_type char(1) default null,
        animal_age double default null,
        post_loc_latitude decimal(16, 8) default null,
        post_loc_longtitude decimal(16, 8) default null,
        post_time_stamp timestamp default CURRENT_TIMESTAMP,
        user_id CHAR(36) default NULL,
        sos_level int default 1, -- 1 --> normal animal , 2 -->  help need   , 3 - immediate help needed 
        qr_image varchar(200) default null,
        animal_id CHAR(36) default null,
        address varchar(400) default 'not given',
        foreign key (user_id) references Users (user_id)
);

create table if not exists rescue_post_image(
    id CHAR(36) primary key default UUID(),
    rescue_post_id CHAR(36),
    rescue_post_image_link varchar(200),
    foreign key(rescue_post_id) references rescue_post(rescue_post_id) on delete cascade
);


CREATE TABLE IF NOT EXISTS animals (
        name varchar(100),
        animal_id CHAR(36) PRIMARY KEY,
        rescue_point_id CHAR(36) default NULL,
        species_type VARCHAR(100) NOT NULL,
        gender_type CHAR(1) DEFAULT NULL,
        age DOUBLE DEFAULT NULL,
        health_status TEXT DEFAULT NULL,
        activity_level VARCHAR(50) DEFAULT NULL,
        emp_id CHAR(36),
        animal_parent_id CHAR(36) default null,
        foreign key (animal_parent_id) references animals(animal_id),
        foreign key (rescue_point_id) references rescue_point (rescue_point_id),
        foreign key (emp_id) references Employee(emp_id)
);


create table if not exists animal_history (
        animal_id CHAR(36) default NULL,
        history_id CHAR(36) primary key,
        level_text varchar(200) not null,
        level_description varchar(1000) not null, 
        sos_level TINYINT default 1,
        created_by varchar(36) not null,
        created_by_type varchar(100) not null default 'user',
        foreign key (animal_id) references animals(animal_id),
        created_at timestamp default CURRENT_TIMESTAMP
);


create table if not exists animal_history_image_upload (
        history_id CHAR(36),
        image_link varchar(200),
        foreign key (history_id) references animal_history (history_id),
        primary key (history_id, image_link)
);

-- create table if not exists animal_history_report(
--     history_id CHAR(36),
--     report_id 
-- )


-- Rescue Point animal registry 

create table if not exists Rescue_point_Animal_Registry(
    animal_id CHAR(36) ,
    rescue_point_id CHAR(36),
    admitted_at timestamp default CURRENT_TIMESTAMP,

    foreign key(rescue_point_id) references rescue_point(rescue_point_id),
    foreign key(animal_id) references animals(animal_id),
    primary key(animal_id , rescue_point_id)

);


-- Employee history management

CREATE TABLE IF NOT EXISTS Employee_history (
    id int AUTO_INCREMENT PRIMARY KEY, 
    emp_id CHAR(36),
    created_at timestamp default CURRENT_TIMESTAMP,
    event_type int ,
    
                                                    -- if event_type = 1 , Employee_created
                                                    -- if event_type = 2 , promoted
                                                    -- if event_type = 3 , demoted
                                                    -- if event_type = 4 , salary Change
                                                    -- if event_type = 5 , supervisor_assigned
                                                    -- if event_type = 6 , supervisor_removed
                                                    -- if event_type = 7 , assigned_at a point
                                                    -- if event_type = 8 , remove from a rescue point
                                                    -- if event_type = 9 , assigned an animal
                                                    -- if event_type = 10 , resigned
                                                    -- if event_type = 11 , assigned as a manager at a rescue point
    animal_id CHAR(16) default NULL,
    rank_assigned_by CHAR(36) default NULL,
    supervisor_id CHAR(36) default NULL,
    rescue_point_id CHAR(36) default NULL,
    emp_rank INT,
    salary decimal (16 , 8),
    reason text
);




create table if not exists EmployeeNotification(
        notification_id CHAR(36) PRIMARY KEY default UUID(),
        emp_id CHAR(36) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP default CURRENT_TIMESTAMP,
        created_by CHAR(36),
        foreign key (created_by) references Employee(emp_id),
        foreign key (emp_id) references Employee(emp_id)
);




-- rescue volunteer management

CREATE TABLE IF NOT EXISTS rescued_event (
        rescue_post_id CHAR(36) default NULL,
        volunteer_id CHAR(36) default NULL,
        FOREIGN KEY (rescue_post_id) REFERENCES rescue_post (rescue_post_id),
        FOREIGN KEY (volunteer_id) REFERENCES volunteers (volunteer_id),
        PRIMARY KEY (rescue_post_id, volunteer_id)
);


-- Adoption Management

CREATE TABLE IF NOT EXISTS shelter_animals(
    animal_id CHAR(36) primary key,
    rescue_point_id CHAR(36),
    animal_name VARCHAR(200) UNIQUE ,
    animal_age DECIMAL(10,1),
    manager CHAR(36) default null,
    is_removed TINYINT DEFAULT 0, 
    health_status TINYINT default 1,-- 1 -> normal 2 -> attention needed 3 -> Emergency,
    added_at timestamp default CURRENT_TIMESTAMP,
    foreign key (rescue_point_id) references rescue_point(rescue_point_id),
    foreign key (manager) references Employee(emp_id)
);

CREATE TABLE IF NOT EXISTS shelter_animals_images(
    id int AUTO_INCREMENT primary key,
    animal_id CHAR(36),
    image_path VARCHAR(200),
    foreign key (animal_id) references shelter_animals(animal_id) on delete cascade
);

CREATE TABLE IF NOT EXISTS Adoption_animals(
    animal_id CHAR(36) primary key ,
    shelter_id CHAR(36),
    foreign key (shelter_id) references shelter_animals(animal_id) on delete cascade,
    created_at TIMESTAMP default CURRENT_TIMESTAMP
);



CREATE TABLE IF NOT EXISTS shelter_animal_Property(
    animal_id CHAR(36),
    property_type varchar(200) , -- vaccination , health , activity , affectionate level
    animal_property VARCHAR(100),
    foreign key (animal_id) references shelter_animals(animal_id) on DELETE CASCADE,
    primary key(animal_id , animal_property)
);

CREATE TABLE IF NOT EXISTS Adoption_Application(
    animal_id CHAR(36),
    user_id CHAR(36),
    adoption_application_id CHAR(36) default UUID(),
    adoption_Application_text VARCHAR(2000) default 'no text',
    adoption_application_status ENUM('pending' , 'accepted' , 'rejected') default 'pending',
    foreign key (animal_id) references Adoption_animals(animal_id) on delete cascade,
    foreign key (user_id) references Users(user_id),
    primary key (adoption_application_id),
    Unique(animal_id , user_id),
    created_at timestamp default CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS adoption_queue (
        queue_id CHAR(36) PRIMARY KEY,
        checked_until timestamp,

        adoption_application_id CHAR(36) ,
        FOREIGN KEY (adoption_application_id) REFERENCES Adoption_Application(adoption_application_id)
);

-- create table if not exists UserNotification (
--         notification_id CHAR(36) PRIMARY KEY,
--         user_id CHAR(36) NOT NULL,
--         message TEXT NOT NULL,
--         related_post_id CHAR(36) DEFAULT NULL,
--         notification_type ENUM ('rescue', 'adoption', 'queue', 'general') DEFAULT 'general',
--         is_read BOOLEAN DEFAULT FALSE,
--         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--         FOREIGN KEY (user_id) REFERENCES Users (user_id)
-- );


-- create table if not exists like_registry_adoption_post (
--         adoption_post_id CHAR(36),
--         user_id CHAR(36),
--         like_type int default 1,
--         foreign key (adoption_post_id) references adoption_post (adoption_post_id),
--         foreign key (user_id) references Users (user_id),
--         primary key (adoption_post_id, like_type)
-- );

-- create table if not exists comment_registry_adoption_post (
--         comment_id CHAR(36) primary key,
--         adoption_post_id CHAR(36),
--         user_id CHAR(36),
--         comment_text text,
--         replying_to CHAR(36) default null,
--         foreign key (adoption_post_id) references adoption_post (adoption_post_id),
--         foreign key (user_id) references Users (user_id),
--         foreign key (replying_to) references comment_registry_adoption_post (comment_id)
-- );





--  3rd party pet centers (gromming center , veterenarian hospital , park , pet friendly resturants)

create table if not exists PetCenters(
    id char(36) PRIMARY KEY DEFAULT (UUID()),
    Name varchar(200),
    lat decimal(12, 6),
    lng decimal(12, 6),
    type ENUM('gromming center', 'veterenarian hospital', 'park', 'other'),
    center_email varchar(200) default null,
    center_contact_number varchar(200) default null          
);

create table if not exists PetCenterImages(
    ImageID char(36) PRIMARY KEY DEFAULT(UUID()),
    id char(36),
    image_path varchar(200) not null,
    foreign key (id) references PetCenters(id)
);



-- Market Place Management


CREATE TABLE if not exists products (
    product_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    name VARCHAR(150) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    rating decimal(10 , 5),
    rating_count int ,
    check(stock >= 0 ),
    is_deleted INT default 0 ,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

create table if not exists product_images(
    id INT AUTO_INCREMENT primary key,
    product_id CHAR(36),
    image_path varchar(200) ,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    foreign key(product_id) references products(product_id)
);

create table if not exists product_category(
    
    product_id CHAR(36) ,
    type varchar(100),
    foreign key(product_id) references products(product_id),
    primary key (product_id , type)
);


CREATE TABLE if not EXISTS product_history (
    id int  AUTO_INCREMENT PRIMARY KEY ,
    product_id CHAR(36),
    old_price DECIMAL(10,2) ,
    new_price DECIMAL(10 ,2) ,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE IF NOT EXISTS  orders (
    order_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    user_id CHAR(36),
    total_amount DECIMAL(10,2) DEFAULT 0,
    is_delivered INT ,  -- 1 -> not delivered , 2 -> delivered
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE if NOT EXISTS order_items (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    order_id CHAR(36),
    product_id CHAR(36),
    quantity INT NOT NULL,
    price_at_purchase DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE if not exists sales_history (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    product_id CHAR(36),
    quantity INT,
    sold_price DECIMAL(10,2),
    sold_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- foreign key Constraint

Alter table rescue_point add constraint rescue_point_supervisor
foreign key (supervisor_id) references Employee (emp_id);


ALTER TABLE Employee_history ADD constraint supervisor_constraint 
foreign key (rank_assigned_by) references Employee(emp_id);

ALTER TABLE Employee_history ADD constraint employee_constraint
foreign key (emp_id) references Employee(emp_id);

ALTER TABLE Employee_history ADD constraint animal_constraint
foreign key (animal_id) references animals(animal_id);

ALTER TABLE rescue_post add constraint fk_animal_constraint
foreign key (animal_id) references animals(animal_id);

-- Check Constraint



-- Triggers
use stray_rescue;

CREATE TRIGGER if not exists before_insert_users BEFORE INSERT ON Users FOR EACH ROW
SET
    NEW.user_id = UUID();

-- CREATE TRIGGER if not exists before_insert_employee BEFORE INSERT ON Employee FOR EACH ROW
-- SET
--     NEW.emp_id = UUID();


CREATE TRIGGER if not exists before_insert_volunteers BEFORE INSERT ON volunteers FOR EACH ROW
SET
    NEW.volunteer_id = UUID();

CREATE TRIGGER if not exists before_insert_rescue_point BEFORE INSERT ON rescue_point FOR EACH ROW
SET
    NEW.rescue_point_id = UUID ();




-- indexes for tables
-- Employee


CREATE INDEX if not exists idx_emp_rank ON Employee (emp_rank);

CREATE INDEX if not exists idx_employee_name ON Employee (emp_name);

CREATE INDEX if not exists idx_rescue_point_geo ON rescue_point (
    rescue_point_location_latitude,
    rescue_point_location_longtitude
);

CREATE INDEX if not exists idx_rescue_post_sos ON rescue_post (sos_level);

CREATE INDEX if not exists idx_animal_species ON animals (species_type);

CREATE INDEX if not exists idx_rescue_point_name ON rescue_point (rescue_point_name);

-- testing sql queries
insert ignore into email_verification (
    email_verification_id,
    email_id,
    table_name,
    is_verified
)
values
    ('a', 'a', 'Users', 'Y');

insert ignore into email_verification (
    email_verification_id,
    email_id,
    table_name,
    is_verified
)
values
    ('b', 'b', 'Employee', 'Y');

insert ignore into email_verification (
    email_verification_id,
    email_id,
    table_name,
    is_verified
)
values
    ('c', 'c', 'volunteers', "Y");

insert ignore into Users (user_name, email, password)
values
    ('a', 'a', 'a');

insert ignore into Employee (emp_name, email, password, emp_rank)
values
    ('b', 'b', 'b', 0);

insert ignore into Employee (emp_name, email, password, emp_rank)
values
    ('b1', 'b1', 'b1', 4);

insert ignore into Employee (emp_name, email, password, emp_rank)
values
    ('b2', 'b2', 'b2', 3);

insert ignore into Employee (emp_name, email, password, emp_rank)
values
    ('b3', 'b3', 'b3', 2);

insert ignore into volunteers (volunteer_name, email, password)
values
    ('c', 'c', 'c');

-- functions
-- levenstein string distance function for finding distance between two strings


DELIMITER $$
CREATE FUNCTION levenshtein(s1 VARCHAR(255), s2 VARCHAR(255))
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE s1_len, s2_len, i, j, cost INT;
    DECLARE lastdiag, olddiag INT;
    DECLARE s1_char CHAR;
    DECLARE cv0 VARCHAR(256);
    DECLARE cv1 VARCHAR(256);
    SET s1_len = CHAR_LENGTH(s1);
    SET s2_len = CHAR_LENGTH(s2);
    IF s1 = s2 THEN
        RETURN 0;
    END IF;
    IF s1_len = 0 THEN
        RETURN s2_len;
    END IF;
    IF s2_len = 0 THEN
        RETURN s1_len;
    END IF;
    SET j = 0;
    SET cv1 = '';
    WHILE j <= s2_len DO
        SET cv1 = CONCAT(cv1, CHAR(j));
        SET j = j + 1;
    END WHILE;
    SET i = 1;
    WHILE i <= s1_len DO
        SET s1_char = SUBSTRING(s1, i, 1);
        SET cv0 = CHAR(i);
        SET lastdiag = i - 1;
        SET j = 1;
        WHILE j <= s2_len DO
            IF s1_char = SUBSTRING(s2, j, 1) THEN
                SET cost = 0;
            ELSE
                SET cost = 1;
            END IF;
            SET olddiag = ORD(SUBSTRING(cv1, j, 1));
            SET lastdiag = LEAST(
                ORD(SUBSTRING(cv1, j + 1, 1)) + 1,
                ORD(SUBSTRING(cv0, j, 1)) + 1,
                olddiag + cost
            );
            SET cv0 = CONCAT(cv0, CHAR(lastdiag));
            SET j = j + 1;
        END WHILE;
        SET cv1 = cv0;
        SET i = i + 1;
    END WHILE;
    RETURN lastdiag;
END$$
DELIMITER ;
