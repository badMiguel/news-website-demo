DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS news;
DROP TABLE IF EXISTS comment;
DROP TABLE IF EXISTS category;
DROP TABLE IF EXISTS news_category;

CREATE TABLE user (
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_name TEXT UNIQUE NOT NULL,
    full_name TEXT NOT NULL,
    hashed_password TEXT NOT NULL,
    salt TEXT NOT NULL,
    privilege INTEGER DEFAULT 0,
    joined_date DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE news ( 
    news_id INTEGER PRIMARY KEY AUTOINCREMENT,
    news_title TEXT UNIQUE NOT NULL,
    news_subtitle TEXT NOT NULL,
    body TEXT NOT NULL,
    author_id INTEGER NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    edited_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    image_path TEXT, 
    comments_enabled INTEGER DEFAULT 1,
    FOREIGN KEY(author_id) REFERENCES user(user_id) ON DELETE SET NULL
);

CREATE TABLE comment (
    comment_id INTEGER PRIMARY KEY AUTOINCREMENT,
    comment TEXT NOT NULL, 
    commentor INTEGER, 
    news_id INTEGER NOT NULL,
    parent_comment_id INTEGER,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(commentor) REFERENCES user(user_id) ON DELETE CASCADE,
    FOREIGN KEY(news_id) REFERENCES news(news_id) ON DELETE CASCADE,
    FOREIGN KEY(parent_comment_id) REFERENCES comment(comment_id) ON DELETE CASCADE
);

CREATE TABLE category (
    category_id INTEGER PRIMARY KEY AUTOINCREMENT,
    category TEXT NOT NULL UNIQUE
);

CREATE TABLE news_category (
    news_id INTEGER,
    category_id INTEGER,
    PRIMARY KEY (news_id, category_id),
    FOREIGN KEY (news_id) REFERENCES news(news_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES category(category_id) ON DELETE CASCADE
);
