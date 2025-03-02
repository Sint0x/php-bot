<?php

require_once 'BaseModel.php';

class UserModel extends BaseModel
{
    public $id = false;
    public $telegram_id;


    public static function get($pdo, $telegramId)
    {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE telegram_id = :telegram_id");
        $stmt->execute(['telegram_id' => $telegramId]);
        $user = $stmt->fetch();
        
        return $user ? new UserModel($user) : false;
    }

    public function create($pdo)
    {
        $stmt = $pdo->prepare("INSERT INTO users (telegram_id) VALUES (:telegram_id)");
        $stmt->execute(['telegram_id' => $this->telegram_id]);
    }
}
