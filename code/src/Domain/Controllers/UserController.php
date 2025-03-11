<?php

namespace Geekbrains\Application1\Domain\Controllers;

use Geekbrains\Application1\Application\Application;
use Geekbrains\Application1\Application\Render;
use Geekbrains\Application1\Domain\Models\User;
use Geekbrains\Application1\Application\Auth;

class UserController extends AbstractController {
    protected array $actionsPermissions = [
        'actionHash' => ['admin', 'some'],
        'actionSave' => ['admin']
    ];

    // Метод для отображения списка пользователей
    public function actionIndex(): string {
        $users = User::getAllUsersFromStorage();
        $render = new Render();

        if (!$users) {
            return $render->renderPage(
                'user-empty.tpl', 
                [
                    'title' => 'Список пользователей в хранилище',
                    'message' => "Список пуст или не найден"
                ]
            );
        } else {
            return $render->renderPage(
                'user-index.tpl', 
                [
                    'title' => 'Список пользователей в хранилище',
                    'users' => $users
                ]
            );
        }
    }

    // Метод для сохранения нового пользователя
    public function actionSave(): string {
        if (User::validateRequestData()) {
            $user = new User();
            $user->setParamsFromRequestData();
            $user->saveToStorage();

            $render = new Render();
            return $render->renderPage(
                'user-created.tpl', 
                [
                    'title' => 'Пользователь создан',
                    'message' => "Создан пользователь " . $user->getUserName() . " " . $user->getUserLastName()
                ]
            );
        } else {
            throw new \Exception("Переданные данные некорректны");
        }
    }

    // Метод для редактирования пользователя
    public function actionEdit(): string {
        $userId = $_GET['user_id'] ?? null;
        if (!$userId) {
            throw new \Exception("Идентификатор пользователя не передан.", 400);
        }
    
        $user = User::getUserById($userId);
        if (!$user) {
            throw new \Exception("Пользователь не найден.", 404);
        }
    
        $render = new Render();
        return $render->renderPageWithForm(
            'user-form.tpl',
            [
                'title' => 'Редактировать пользователя',
                'user' => $user
            ]
        );
    }
    

    // Метод для обновления данных пользователя
    public function actionUpdate(): string {
        if (!User::validateRequestData()) {
            throw new \Exception("Переданные данные некорректны.", 400);
        }
    
        $userId = $_POST['user_id']; // получение ID пользователя
        $user = User::getUserById($userId);
        if (!$user) {
            throw new \Exception("Пользователь не найден.", 404);
        }
    
        $user->setParamsFromRequestData();
        $user->updateInStorage(); 
    
        $render = new Render();
        return $render->renderPage(
            'user-updated.tpl',
            [
                'title' => 'Пользователь обновлен',
                'message' => "Пользователь " . $user->getUserName() . " обновлен."
            ]
        );
    }
    // Метод для удаления пользователя
    public function actionDelete(): string {
        $userId = $_GET['user_id'] ?? null;
        if (!$userId) {
            throw new \Exception("Идентификатор пользователя не передан.", 400);
        }
    
        if (!User::deleteUserById($userId)) { // Проверка, был ли действительно удалён пользователь
            throw new \Exception("Пользователь не найден.", 404);
        }
    
        header('Location: /user');
        exit();
    }
    // Другие методы контроллера
    public function actionAuth(): string {
        $render = new Render();
        
        return $render->renderPageWithForm(
            'user-auth.tpl', 
            [
                'title' => 'Форма логина',
            ]
        );
    }

    public function actionHash(): string {
        return Auth::getPasswordHash($_GET['pass_string']);
    }

    public function actionLogin(): string {
        if (isset($_POST['login']) && isset($_POST['password'])) {
            $result = Application::$auth->proceedAuth($_POST['login'], $_POST['password']);
    
            if ($result) {
                // Проверяем, установлен ли чекбокс "Запомнить меня"
                if (isset($_POST['remember_me'])) {
                    // Устанавливаем куки на 30 дней
                    setcookie('user_login', $_POST['login'], time() + (86400 * 30), "/"); // 86400 = 1 день
                }
    
                header('Location: /');
                return "";
            } else {
                $render = new Render();
                return $render->renderPageWithForm(
                    'user-auth.tpl',
                    [
                        'title' => 'Форма логина',
                        'auth-success' => false,
                        'auth-error' => 'Неверные логин или пароль'
                    ]
                );
            }
        }
        throw new \Exception("Пожалуйста, введите логин и пароль.");
    }
}
