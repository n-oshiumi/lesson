<?php
require_once __DIR__ . "/DBConnetion.php";

$dbh = DbConnection::connect();

// テーブル削除
$create_user_sql = 'DROP TABLE IF EXISTS comments, threads, users';
$dbh->query($create_user_sql);


// usersテーブル作成
$create_user_sql = 'CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
    name VARCHAR(20) NOT NULL,
    email VARCHAR(191) UNIQUE NOT NULL UNIQUE,
    thumbnail_url TEXT,
    password TEXT NOT NULL,
    created_at DATETIME
) engine=innodb default charset=utf8';
$dbh->query($create_user_sql);


// threadsテーブル作成
$create_threads_sql = 'CREATE TABLE threads (
    id INT(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
    user_id INT(11) NOT NULL,
    content VARCHAR(140) NOT NULL,
    image_url TEXT,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users (id)
) engine=innodb default charset=utf8';
$dbh->query($create_threads_sql);

// commentsテーブル作成
$create_comments_sql = 'CREATE TABLE comments (
    id INT(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
    user_id INT(11) NOT NULL,
    thread_id INT(11) NOT NULL,
    content VARCHAR(140) NOT NULL,
    image_url TEXT,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (thread_id) REFERENCES threads (id),
    FOREIGN KEY (user_id) REFERENCES users (id)
) engine=innodb default charset=utf8';
$dbh->query($create_comments_sql);

if ($dbh->errorInfo()[0] !== "00000") {
    var_dump($dbh->errorInfo());
}


// 接続を閉じる
$dbh = null;
