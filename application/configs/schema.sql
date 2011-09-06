
CREATE TABLE IF NOT EXISTS session (
    id char(32) PRIMARY KEY,
    modified int,
    lifetime int,
    data text
);

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


CREATE TABLE IF NOT EXISTS profile (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER UNIQUE NOT NULL,
    location VARCHAR(64),
    about_body TEXT,
    interests_body TEXT,
    website_url VARCHAR(255),
    blog_url VARCHAR(255),
    blog_feed_url VARCHAR(255),
    INDEX (user_id), INDEX (location),
    FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS contact (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    profile_id INTEGER NOT NULL,
    service VARCHAR(64) NOT NULL,
    value VARCHAR(255) NOT NULL,
    INDEX (profile_id), INDEX (service), INDEX (value),
    FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS entity (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
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
    user_agent VARCHAR(255),
    created_at DATETIME,
    INDEX (ip_address), INDEX (user_agent), INDEX (created_at),
    FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS entity_vote (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    entity_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    value INTEGER,
    INDEX (value),
    FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS upload (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    filename VARCHAR(255) NOT NULL,
    mime_type VARCHAR(64),
    size INTEGER,
    user_id INTEGER,
    is_temporary INTEGER NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    INDEX (mime_type), INDEX (size), INDEX (is_temporary), INDEX (created_at),
    FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS post (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    type VARCHAR(32) NOT NULL,
    entity_id INTEGER NOT NULL,
    creator_id INTEGER,
    created_at DATETIME,
    creator_comment TEXT,
    review_status VARCHAR(32),
    reviewer_id INTEGER,
    reviewed_at DATETIME,
    reviewer_comment TEXT,
    status VARCHAR(32),
    public_id INTEGER,
    version INTEGER DEFAULT 1,
    is_latest TINYINT(1) DEFAULT 1,
    author_id INTEGER,
    when_at DATETIME,
    title TEXT,    
    slug VARCHAR(128),    
    is_featured TINYINT(1) DEFAULT 0,
    summary TEXT,
    body TEXT,
    INDEX (type), INDEX (review_status), INDEX (created_at), INDEX (reviewed_at),
    INDEX (status), INDEX (public_id), INDEX (version), INDEX (is_latest),
    INDEX (when_at), INDEX (slug), INDEX (is_featured), 
    FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE,
    FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE SET NULL,
    FOREIGN KEY (reviewer_id) REFERENCES user (id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS post_article (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    post_id INTEGER NOT NULL,
    FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS post_file (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    post_id INTEGER NOT NULL,
    file_url VARCHAR(255),
    FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS post_link (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    post_id INTEGER NOT NULL,
    url VARCHAR(255),
    FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS post_event (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    post_id INTEGER NOT NULL,
    type VARCHAR(32),
    location VARCHAR(128),
    start_date DATE,
    end_date DATE,
    url VARCHAR(255),
    INDEX (type), INDEX (location), 
    INDEX (start_date), INDEX (end_date),
    FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE
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
    post_id INTEGER NOT NULL,
    forum_id INTEGER,
    FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE,
    FOREIGN KEY (forum_id) REFERENCES forum (id) ON DELETE SET NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS post_comment (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    post_id INTEGER NOT NULL,
    root_entity_id INTEGER,
    parent_entity_id INTEGER,
    name VARCHAR(128),
    email VARCHAR(128),
    url VARCHAR(255),
    FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE,
    FOREIGN KEY (root_entity_id) REFERENCES entity (id) ON DELETE SET NULL,
    FOREIGN KEY (parent_entity_id) REFERENCES entity (id) ON DELETE SET NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS post_paper (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    post_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    authors TEXT NOT NULL,
    source VARCHAR(128),
    publish_date DATE,
    url VARCHAR(255) NOT NULL,
    pubmed_id INTEGER,
    abstract TEXT,
    INDEX (pubmed_id), INDEX (title(100)), INDEX (authors(100)), INDEX (publish_date),
    FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS post_lab (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    post_id INTEGER NOT NULL,
    name VARCHAR(255),
    type VARCHAR(32),
    location VARCHAR(255),
    url VARCHAR(255),
    INDEX (type), INDEX (name), INDEX (location),
    FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS post_person (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    post_id INTEGER NOT NULL,
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    lab_id INTEGER,
    user_id INTEGER,
    email VARCHAR(255),
    lab_name VARCHAR(128),
    location VARCHAR(64),
    personal_url VARCHAR(255),
    lab_url VARCHAR(255),
    INDEX (lab_id), INDEX (lab_name),
    INDEX (last_name, first_name), INDEX (email), INDEX (location),
    FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE,
    FOREIGN KEY (lab_id) REFERENCES post_lab (id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE SET NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS tag (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    value VARCHAR(64),
    label VARCHAR(64),
    INDEX (id), INDEX (value)
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS tag_heiarchy (
    namespace VARCHAR(64),
    tag_id INTEGER NOT NULL,    
    parent_tag_id INTEGER NOT NULL,    
    PRIMARY KEY (tag_id, parent_tag_id), INDEX (namespace),
    FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE,
    FOREIGN KEY (parent_tag_id) REFERENCES tag (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS post_tag (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    post_id INTEGER NOT NULL,
    tag_id INTEGER NOT NULL,
    creator_id INTEGER,
    review_status VARCHAR(32),
    INDEX (review_status),
    FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE,
    FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;



CREATE TABLE IF NOT EXISTS post_flag (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    post_id INTEGER NOT NULL,
    creator_id INTEGER NOT NULL,
    created_at DATETIME NOT NULL,
    type varchar(32),
    comment TEXT,
    INDEX (created_at), INDEX (type),
    FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE,
    FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS announcement (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    created_at DATETIME,
    body TEXT,
    INDEX (created_at)
) ENGINE = INNODB DEFAULT CHARSET = utf8;
