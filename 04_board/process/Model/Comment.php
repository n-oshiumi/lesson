<?php

require_once __DIR__ . "/BaseModel.php";

class Comment extends BaseModel
{
    private $tableName = 'comments';

    public function getComments($thread_id)
    {
        return $this->dbh->query('SELECT *, comments.id as `comment.id`,comments.created_at as `comments.created_at`, comments.updated_at as `comments.updated_at` FROM comments INNER JOIN users on comments.user_id = users.id WHERE comments.thread_id = ' . $thread_id . ' ORDER BY comments.created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteComments($thread_id)
    {
        return $this->dbh->query('DELETE FROM comments WHERE thread_id = "' . $thread_id . '"');
    }

    public function deleteComment($comment_id, $user_id)
    {
        return $this->dbh->query('DELETE c FROM comments c INNER JOIN users on c.user_id = users.id WHERE c.id = "' . $comment_id . '" AND users.id = "' . $user_id . '"');
    }

    public function create($thread_id, $comment, $image_url)
    {

        $user = Auth::getLoginUser();

        $stmt = $this->dbh->prepare("insert into comments(user_id,thread_id,content,image_url,created_at,updated_at) values(?,?,?,?,?,?)");
        $stmt->bindValue(1, $user['id']);
        $stmt->bindValue(2, $thread_id);
        $stmt->bindValue(3, $comment);
        $stmt->bindValue(4, $image_url);

        $now = date("Y/m/d H:i:s");
        $stmt->bindValue(5, $now);
        $stmt->bindValue(6, $now);

        return ($stmt->execute()) ? true : false;
    }

    public function updateComment($comment_id, $user_id, $content, $image_url)
    {
        return $this->dbh->query('UPDATE comments c INNER JOIN users u on c.user_id = u.id SET c.content = "' . $content . '", c.image_url = "' . $image_url . '" WHERE c.id = "' . $comment_id . '" AND u.id = "' . $user_id . '"');
    }

    public function removeImg($comment_id)
    {
        return $this->dbh->query('UPDATE comments SET image_url = NULL WHERE id = ' . $comment_id);
    }
}

