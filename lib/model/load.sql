-- Insert data into the user table
INSERT INTO user (user_name, hashed_password, salt, privilege) VALUES ('alice', 'hashed_pw1', 'salt1', 1);
INSERT INTO user (user_name, hashed_password, salt, privilege) VALUES ('bob', 'hashed_pw2', 'salt2', 0);
INSERT INTO user (user_name, hashed_password, salt, privilege) VALUES ('charlie', 'hashed_pw3', 'salt3', 0);

-- Insert data into the news table
INSERT INTO news (news_title, body, author_id) VALUES ('First News', 'This is the body of the first news.', 1);
INSERT INTO news (news_title, body, author_id) VALUES ('Second News', 'This is the body of the second news.', 2);
INSERT INTO news (news_title, body, author_id) VALUES ('Third News', 'This is the body of the third news.', 1);

-- Insert data into the comment table
INSERT INTO comment (comment, commentor, news_id) VALUES ('Great article!', 2, 1);
INSERT INTO comment (comment, commentor, news_id) VALUES ('Interesting read.', 3, 1);
INSERT INTO comment (comment, commentor, news_id) VALUES ('I disagree with this.', 1, 2);
INSERT INTO comment (comment, commentor, news_id) VALUES ('Thanks for sharing.', 3, 3);
