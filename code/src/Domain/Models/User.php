<?php

namespace Geekbrains\Application1\Domain\Models;

use Geekbrains\Application1\Application\Application;

class User {
    private ?string $userName;
    private ?string $userLastName;
    private ?int $userBirthday;
    private ?int $idUser; // Добавляем свойство для хранения ID пользователя

    public function __construct(string $name = null, string $lastName = null, int $birthday = null, int $idUser = null) {
        $this->userName = $name;
        $this->userLastName = $lastName;
        $this->userBirthday = $birthday;
        $this->idUser = $idUser; // Инициализируем ID пользователя
    }

    // Установить имя
    public function setName(string $userName) : void {
        $this->userName = $userName;
    }

    // Установить фамилию
    public function setLastName(string $userLastName) : void {
        $this->userLastName = $userLastName;
    }

    // Получить имя
    public function getUserName(): string {
        return $this->userName;
    }

    // Получить фамилию
    public function getUserLastName(): string {
        return $this->userLastName;
    }

    // Получить день рождения
    public function getUserBirthday(): int {
        return $this->userBirthday;
    }

    // Установить день рождения
    public function setBirthdayFromString(string $birthdayString) : void {
        $this->userBirthday = strtotime($birthdayString);
    }

    // Получить всех пользователей из базы данных
    public static function getAllUsersFromStorage(): array {
        $sql = "SELECT * FROM users";
        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute();
        $result = $handler->fetchAll();

        $users = [];
        foreach ($result as $item) {
            $user = new User($item['user_name'], $item['user_lastname'], $item['user_birthday_timestamp']);
            $users[] = $user;
        }
        
        return $users;
    }

    // Валидация данных из запроса
    public static function validateRequestData(): bool {
        $result = true;

        if (!(
            isset($_POST['name']) && !empty($_POST['name']) &&
            isset($_POST['lastname']) && !empty($_POST['lastname']) &&
            isset($_POST['birthday']) && !empty($_POST['birthday'])
        )) {
            $result = false;
        }

        if (!preg_match('/^(\d{2}-\d{2}-\d{4})$/', $_POST['birthday'])) {
            $result = false;
        }

        if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] != $_POST['csrf_token']) {
            $result = false;
        }

        return $result;
    }

    // Установить параметры из запроса
    public function setParamsFromRequestData(): void {
        $this->userName = htmlspecialchars($_POST['name']);
        $this->userLastName = htmlspecialchars($_POST['lastname']);
        $this->setBirthdayFromString($_POST['birthday']); 
    }

    // Сохранить пользователя в базу данных
    public function saveToStorage(): void {
        $sql = "INSERT INTO users(user_name, user_lastname, user_birthday_timestamp) VALUES (:user_name, :user_lastname, :user_birthday)";
        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute([
            'user_name' => $this->userName,
            'user_lastname' => $this->userLastName,
            'user_birthday' => $this->userBirthday
        ]);
    }

    // Получить пользователя по ID
    public static function getUserById(int $userId): ?User {
        $sql = "SELECT * FROM users WHERE id_user = :id_user";
        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute(['id_user' => $userId]);
        $result = $handler->fetch();

        if ($result) {
            return new User($result['user_name'], $result['user_lastname'], $result['user_birthday_timestamp']);
        }
        return null; // Если пользователь не найден
    }

    // Обновить данные пользователя в базе данных
    public function updateInStorage(): void {
        // Здесь необходимо добавить поле ID пользователя
        $sql = "UPDATE users SET user_name = :user_name, user_lastname = :user_lastname, user_birthday_timestamp = :user_birthday WHERE id_user = :id_user";

        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute([
            'user_name' => $this->userName,
            'user_lastname' => $this->userLastName,
            'user_birthday' => $this->userBirthday,
            'id_user' => $this->idUser // Не забудьте передать ID пользователя
        ]);
    }

    // Удалить пользователя по ID
    public static function deleteUserById(int $userId): void {
        $sql = "DELETE FROM users WHERE id_user = :id_user";
        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute(['id_user' => $userId]);
    }
}