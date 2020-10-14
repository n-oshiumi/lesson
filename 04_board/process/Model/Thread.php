<?php

require_once __DIR__ . "/BaseModel.php";

class Thread extends BaseModel
{
    private $tableName = 'threads';

    public function getThread($id)
    {
        return $this->dbh->query('SELECT *, threads.id as `thread.id`,threads.created_at as `thread.created_at`, threads.updated_at as `thread.updated_at` FROM threads INNER JOIN users on threads.user_id = users.id WHERE threads.id="' . $id . '"')->fetch(PDO::FETCH_ASSOC);
    }

    public function getThreadByIdAndUser($id, $user_id)
    {
        return $this->dbh->query('SELECT *, threads.id as `thread.id`,threads.created_at as `thread.created_at`, threads.updated_at as `thread.updated_at` FROM threads INNER JOIN users on threads.user_id = users.id WHERE threads.id="' . $id . '" AND users.id="' . $user_id . '" ')->fetch(PDO::FETCH_ASSOC);
    }
    public function getThreads()
    {
        return $this->dbh->query('SELECT *, threads.id as `thread.id`,threads.created_at as `thread.created_at`, threads.updated_at as `thread.updated_at` FROM threads INNER JOIN users on threads.user_id = users.id ORDER BY threads.created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($content, $image_url = null)
    {
        $user = Auth::getLoginUser();

        $stmt = $this->dbh->prepare("insert into threads(user_id,content,image_url,created_at,updated_at) values(?,?,?,?,?)");
        $stmt->bindValue(1, $user['id']);
        $stmt->bindValue(2, $content);
        $stmt->bindValue(3, $image_url);

        $now = date("Y/m/d H:i:s");
        $stmt->bindValue(4, $now);
        $stmt->bindValue(5, $now);

        return ($stmt->execute()) ? true : false;
    }

    public function deleteThread($id, $user_id)
    {
        return $this->dbh->query('DELETE t FROM threads t INNER JOIN users on t.user_id = users.id WHERE t.id = "' . $id . '" AND users.id = "' . $user_id . '"');
    }

    public function update($id, $user_id, $content, $image_url)
    {
        return $this->dbh->query('UPDATE threads t INNER JOIN users u on t.user_id = u.id SET t.content = "' . $content . '", image_url = "' . $image_url . '" WHERE t.id = "' . $id . '" AND u.id = "' . $user_id . '"');
    }

    public function removeImg($thread_id)
    {
        return $this->dbh->query('UPDATE threads SET image_url = NULL WHERE id = ' . $thread_id);
    }
}
