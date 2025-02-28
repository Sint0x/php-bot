<?php

require 'vendor/autoload.php';
require_once 'Models/UserModel.php';
require_once 'Models/PaymentModel.php';
require_once 'Methods/PaymentMethods.php';

use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Update;


class TelegramBot
{
    private $bot;
    private $pdo;

    public function __construct($token, $pdo)
    {
        $this->bot = new Client($token);
        $this->pdo = $pdo;
    }

    public function run()
    {
        $this->bot->on(function (Update $update) {
            $this->onMessageEvent($update);
        }, fn() => true);

        $this->bot->run();
    }

    private function onMessageEvent(Update $update)
    {
        $message = $update->getMessage();
        $chatId = $message->getChat()->getId();
        $text = $message->getText();

        $user = $this->getUserOrCreate($chatId);

        $this->processMessage($chatId, $text, $user->id);
    }

    private function getUserOrCreate($chatId)
    {
        $user = UserModel::get($this->pdo, $chatId);
        if (!$user){
            $userCls = new UserModel(['telegram_id' => $chatId]);
            $userCls->create($this->pdo);
            $user = UserModel::get($this->pdo, $chatId);
        }
        return $user;
    }
    
    private function processMessage($chatId, $text, $userId)
    {
        $amount = $this->parseAmount($text);
        $currentBalance = PaymentModel::countUpUserBalace($this->pdo, $userId);

        if (!$this->isAmount($amount)) {
            $this->bot->sendMessage($chatId, "Пожалуйста, отправьте ЧИСЛО для изменения баланса.");
        }
        if ($amount < 0 && abs($amount) > $currentBalance) {
            $this->bot->sendMessage($chatId, "Ошибка при списании: недостаточно средств на счёте.");
        } 

        $payment = new PaymentModel(['user_id' => $userId, 'amount' => $amount]);
        $payment->create($this->pdo);
        
        $newBalance = $currentBalance + $amount;
        $this->bot->sendMessage($chatId, "Ваш текущий баланс: $" . number_format($newBalance, 2));
    }

    
    private function isAmount($text): bool
    {
        return is_numeric($text);
    }

    private function parseAmount($text): string
    {
        return str_replace(',', '.', $text);
    }
}