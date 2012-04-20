
CREATE TABLE IF NOT EXISTS user (
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
	username VARCHAR(32) UNIQUE NOT NULL,
	name VARCHAR(64),
	email VARCHAR(128),
	role VARCHAR(32) DEFAULT "member",
	status VARCHAR(32) DEFAULT "active",
	created_at DATETIME,
    seen_at DATETIME,
	password_algorithm VARCHAR(32),
	password_hash VARCHAR(40),
	password_salt VARCHAR(40),
	activation_key VARCHAR(40),
    timezone VARCHAR(32) NOT NULL DEFAULT "America/Los_Angeles",
    locale VARCHAR(32) NOT NULL DEFAULT "en_US",
    language VARCHAR(2) NOT NULL DEFAULT "en",
    newsletter VARCHAR(32) NULL,
    reputation INTEGER NOT NULL DEFAULT 1,
    post_count INTEGER NOT NULL DEFAULT 0,
    INDEX(name), INDEX (role), INDEX (status), INDEX (email),
    INDEX (timezone), INDEX (locale), INDEX (language), INDEX (newsletter),
    INDEX (created_at), INDEX (seen_at), INDEX (reputation), INDEX (post_count)
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS user_profile (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
	user_id INTEGER UNIQUE NOT NULL,
	location VARCHAR(64),
	about_body TEXT,
    interests_body TEXT,
	website_url VARCHAR(255),
    blog_url VARCHAR(255),
    blog_feed_url VARCHAR(255),
    email VARCHAR(255),
    linkedin_id VARCHAR(64),
    facebook_id VARCHAR(64),
    aim_id VARCHAR(64),
    yahoo_id VARCHAR(64),
    msn_id VARCHAR(64),
    twitter_id VARCHAR(64),
    in_twitter_stream INTEGER NOT NULL DEFAULT 0,
    INDEX (user_id), INDEX (location),
    FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS user_contact (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER NOT NULL,
	service VARCHAR(64) NOT NULL,
    value VARCHAR(255) NOT NULL,
    INDEX (user_id), INDEX (service), INDEX (value),
	FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;



CREATE TABLE IF NOT EXISTS entity (
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
	type VARCHAR(32),
    rating INTEGER NOT NULL DEFAULT 0,
	up_votes_count INTEGER NOT NULL DEFAULT 0,
	down_votes_count INTEGER NOT NULL DEFAULT 0,
	comments_count INTEGER NOT NULL DEFAULT 0,
    views_count INTEGER NOT NULL DEFAULT 0,
	INDEX (type), INDEX (rating), INDEX (up_votes_count), INDEX (down_votes_count),
    INDEX (comments_count), INDEX (views_count)
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS entity_view (
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
	entity_id INTEGER NOT NULL,
    user_id INTEGER,
    ip_address VARCHAR(40),
    created_at DATETIME,
    INDEX (entity_id), INDEX (user_id), INDEX (ip_address), INDEX (created_at),
    FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS entity_vote (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
	entity_id INTEGER NOT NULL,
	user_id INTEGER NOT NULL,
	value INTEGER,
    INDEX (entity_id), INDEX (user_id), INDEX (value),
	FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE,
	FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS entity_flag (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
	entity_id INTEGER NOT NULL,
	creator_id INTEGER NOT NULL,
    created_at DATETIME NOT NULL,
	type varchar(32),
    comment TEXT,
    INDEX (entity_id), INDEX (creator_id), INDEX (created_at), INDEX (type),
	FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE,
	FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS entity_revision (
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
	entity_id INTEGER NOT NULL,
    status VARCHAR(32),
    creator_id INTEGER,
	created_at DATETIME,
    creator_comment TEXT,
	reviewer_id INTEGER,
    reviewed_at DATETIME,
    reviewer_comment TEXT,
    json_data TEXT,
	INDEX (entity_id), INDEX (status), INDEX (creator_id), INDEX (created_at),
    INDEX (reviewer_id), INDEX (reviewed_at),
	FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE,
	FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE SET NULL,
	FOREIGN KEY (reviewer_id) REFERENCES user (id) ON DELETE SET NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS entity_category (
    entity_id INTEGER NOT NULL,
	value VARCHAR(64) NOT NULL,
    type VARCHAR(32),
    position INTEGER,
	PRIMARY KEY (entity_id, value, type, position), INDEX (type), INDEX (value),
	FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS entity_tag (
    entity_id INTEGER NOT NULL,
	value VARCHAR(64) NOT NULL,
    position INTEGER,
	PRIMARY KEY (entity_id, position, value), INDEX (value),
	FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS category (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    type VARCHAR(32),
    value VARCHAR(64),
    INDEX (id, type, value), INDEX (type), INDEX (value)
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS category_heiarchy (
    category_id INTEGER NOT NULL,
    parent_category_id INTEGER NOT NULL,
    PRIMARY KEY (category_id, parent_category_id), INDEX (parent_category_id)
) ENGINE = INNODB DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS upload (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    filename VARCHAR(255) NOT NULL,
    mime_type VARCHAR(64),
    size INTEGER,
    user_id INTEGER,
    is_temporary INTEGER NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    INDEX (mime_type), INDEX (size), INDEX (user_id),
    INDEX (is_temporary), INDEX (created_at),
    FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8;



CREATE TABLE IF NOT EXISTS post_article (
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
    entity_id INTEGER UNIQUE NOT NULL,
    author_id INTEGER,
    status VARCHAR(32),
	created_at DATETIME,
	updated_at DATETIME,
	title VARCHAR(128),
	slug VARCHAR(128),
	summary TEXT,
    body TEXT,
	is_featured TINYINT(1) NOT NULL DEFAULT 0,
	INDEX (author_id), INDEX (status),
    INDEX (created_at), INDEX (updated_at),
    INDEX (title), INDEX (slug), INDEX (is_featured),
    FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS post_file (
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
    entity_id INTEGER UNIQUE NOT NULL,
    author_id INTEGER,
    status VARCHAR(32),
	created_at DATETIME,
	updated_at DATETIME,
	title VARCHAR(128),
	slug VARCHAR(128),
    upload_id INTEGER,
	summary TEXT,
    body TEXT,
	is_featured TINYINT(1) NOT NULL DEFAULT 0,
	INDEX (author_id), INDEX (status), INDEX (created_at), INDEX (updated_at),
    INDEX (title), INDEX (slug), INDEX (is_featured),
    FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL,
    FOREIGN KEY (upload_id) REFERENCES upload (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS post_link (
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
    entity_id INTEGER UNIQUE NOT NULL,
    author_id INTEGER,
    status VARCHAR(32),
	created_at DATETIME,
    updated_at DATETIME,
	title VARCHAR(128),
	slug VARCHAR(128),
    url VARCHAR(255),
	summary TEXT,
    body TEXT,
	is_featured TINYINT(1) NOT NULL DEFAULT 0,
	INDEX (author_id), INDEX (created_at), INDEX (updated_at),
    INDEX(status), INDEX (title), INDEX (slug), INDEX (is_featured),
    FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS post_content (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    entity_id INTEGER UNIQUE NOT NULL,
    post_id INTEGER NOT NULL,
    author_id INTEGER,
    status VARCHAR(32),
	created_at DATETIME,
    updated_at DATETIME,
    entity_type VARCHAR(32) NOT NULL,
	title VARCHAR(128),
	slug VARCHAR(64),
	summary TEXT,
    body TEXT,
	is_featured TINYINT(1) NOT NULL DEFAULT 0,
	INDEX(post_id), INDEX (author_id), INDEX (status), INDEX (entity_type), INDEX (created_at),
    INDEX (updated_at), INDEX (title), INDEX (slug), INDEX (is_featured),
    FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;



CREATE TABLE IF NOT EXISTS post_event (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    entity_id INTEGER UNIQUE NOT NULL,
    author_id INTEGER,
    status VARCHAR(32),
	created_at DATETIME,
    updated_at DATETIME,
	title VARCHAR(128),
	slug VARCHAR(128),
    type VARCHAR(32),
	location VARCHAR(128),
	starts_at DATE,
	ends_at DATE,
    url VARCHAR(255),
	summary TEXT,
    body TEXT,
	is_featured TINYINT(1) NOT NULL DEFAULT 0,
	INDEX (author_id), INDEX (status), INDEX (created_at), INDEX (updated_at),
    INDEX (title),	INDEX (slug), INDEX (type),	INDEX (location),
    INDEX (starts_at), INDEX (ends_at), INDEX (is_featured),
    FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS post_job (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
	entity_id INTEGER UNIQUE NOT NULL,
    author_id INTEGER,
    status VARCHAR(32),
	created_at DATETIME,
    updated_at DATETIME,
    title VARCHAR(255),
	type VARCHAR(128),
    education_level VARCHAR(128),
    experience_level VARCHAR(128),
    career_level VARCHAR(128),
    location VARCHAR(128),
	expires_at DATETIME,
    body TEXT,
	INDEX (author_id), INDEX (status), INDEX (created_at), INDEX (updated_at),
    INDEX (title), INDEX (type), INDEX (education_level), INDEX (experience_level),
    INDEX (career_level), INDEX (location), INDEX (expires_at),
	FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS forum (
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
	title VARCHAR(128),
    slug VARCHAR(128),
	description TEXT,
	discussions_count INTEGER NOT NULL DEFAULT 0,
	replies_count INTEGER NOT NULL DEFAULT 0,
	last_discussion_id INTEGER,
	INDEX (title), INDEX (slug), INDEX (discussions_count),
	INDEX (replies_count), INDEX (last_discussion_id)
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS post_discussion (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    entity_id INTEGER UNIQUE NOT NULL,
    author_id INTEGER,
    status VARCHAR(32),
	created_at DATETIME,
    updated_at DATETIME,
	forum_id INTEGER,
	title VARCHAR(128),
	slug VARCHAR(128),
    body TEXT,
    INDEX (author_id), INDEX (status), INDEX (created_at), INDEX (updated_at),
	FOREIGN KEY (forum_id) REFERENCES forum (id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8;



CREATE TABLE IF NOT EXISTS post_comment (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
	entity_id INTEGER UNIQUE NOT NULL,
    author_id INTEGER,
    status VARCHAR(32),
	created_at DATETIME,
    updated_at DATETIME,
	root_entity_id INTEGER,
	parent_entity_id INTEGER,
    name VARCHAR(128),
    email VARCHAR(128),
    url VARCHAR(255),
	body TEXT,
    INDEX (status), INDEX (author_id), INDEX (created_at), INDEX (updated_at),
    INDEX (entity_id), INDEX (root_entity_id), INDEX (parent_entity_id),
	FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8;



CREATE TABLE IF NOT EXISTS post_paper (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
	entity_id INTEGER UNIQUE NOT NULL,
    author_id INTEGER,
    status VARCHAR(32),
	created_at DATETIME,
    updated_at DATETIME,
    url VARCHAR(255) NOT NULL,
	pubmed_id INTEGER,
    title TEXT NOT NULL,
	authors TEXT NOT NULL,
	source VARCHAR(128),
	publish_date DATE,
  type VARCHAR(32),
    abstract TEXT,
    summary TEXT,
	INDEX (status), INDEX (author_id), INDEX (created_at), INDEX (updated_at),
    INDEX (pubmed_id), INDEX (title(100)), INDEX (authors(100)),
    INDEX (publish_date), INDEX (type)
	FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS post_lab (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
	entity_id INTEGER UNIQUE NOT NULL,
    author_id INTEGER,
    status VARCHAR(32),
	created_at DATETIME,
    updated_at DATETIME,
    type VARCHAR(32),
	name VARCHAR(128),
	location VARCHAR(64),
	url VARCHAR(255),
	body TEXT,
    INDEX (status), INDEX (author_id), INDEX (created_at), INDEX (updated_at),
    INDEX (name), INDEX (location),
	FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS post_person (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
	entity_id INTEGER UNIQUE NOT NULL,
    author_id INTEGER,
    status VARCHAR(32),
	created_at DATETIME,
    updated_at DATETIME,
    lab_id INTEGER,
	user_id INTEGER,
    first_name VARCHAR(128),
	last_name VARCHAR(128),
	email VARCHAR(255),
	lab_name VARCHAR(128),
	location VARCHAR(64),
	personal_url VARCHAR(255),
    lab_url VARCHAR(255),
	body TEXT,
    INDEX (status), INDEX (author_id), INDEX (created_at), INDEX (updated_at),
    INDEX (lab_id), INDEX (lab_name),
    INDEX (last_name, first_name), INDEX (email), INDEX (location),
	FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE,
	FOREIGN KEY (lab_id) REFERENCES post_lab (id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE SET NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8;



CREATE TABLE IF NOT EXISTS announcement (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    created_at DATETIME,
    body TEXT,
    INDEX (created_at)
) ENGINE = INNODB DEFAULT CHARSET = utf8;






CREATE TABLE IF NOT EXISTS post_question (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    entity_id INTEGER UNIQUE NOT NULL,
    author_id INTEGER,
    status VARCHAR(32),
	created_at DATETIME,
    updated_at DATETIME,
	title VARCHAR(128),
    slug VARCHAR(128),
	body TEXT,
    answer_id INTEGER,
    INDEX (author_id), INDEX (status), INDEX (created_at), INDEX (updated_at),
    INDEX (slug), INDEX (answer_id),
	FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS post_answer (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
	entity_id INTEGER UNIQUE NOT NULL,
    author_id INTEGER,
    status VARCHAR(32),
	created_at DATETIME,
    updated_at DATETIME,
    question_id INTEGER NOT NULL,
	body TEXT,
	INDEX (author_id), INDEX (status), INDEX (created_at), INDEX (updated_at), INDEX (question_id),
	FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE,
	FOREIGN KEY (question_id) REFERENCES post_question (id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8;

ALTER TABLE post_question
ADD FOREIGN KEY (answer_id) REFERENCES post_answer (id) ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS email_archive (
	to_name VARCHAR(128),
	to_email VARCHAR(128),
	from_name VARCHAR(128),
	from_email VARCHAR(128),
	subject VARCHAR(128),
	body_html TEXT,
	body_text TEXT,
	sending_component VARCHAR(32),
	sent_at TIMESTAMP,
	INDEX (to_name), INDEX (to_email), INDEX (from_name), INDEX (from_email),
    INDEX (sending_component)
) ENGINE = INNODB DEFAULT CHARSET = utf8;

