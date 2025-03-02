<?php

require_once 'BaseModel.php';

class PaymentModel extends BaseModel
{
    public $id = false;
    public $user_id;
    public $amount;

    public static function countUpUserBalace($pdo, $userId)
    {
        $stmt = $pdo->prepare("SELECT SUM(amount) AS total_amount FROM transactions WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $totalAmount = $stmt->fetch();
        return $totalAmount;
    }

    public function create($pdo): void
    {
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, amount) VALUES (:user_id, :amount)");
        $stmt->execute(['user_id' => $this->user_id, 'amount' => $this->amount]);
    }
}