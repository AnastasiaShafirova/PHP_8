<?php

namespace Geekbrains\Application1\Domain\Controllers;

use Geekbrains\Application1\Application\Application;
use Geekbrains\Application1\Application\Render;
use Geekbrains\Application1\Domain\Models\User;
use Geekbrains\Application1\Application\Auth;

class UserController extends AbstractController {
    protected array $actionsPermissions = [
        'actionHash' => ['admin', 'some'],
        'actionSave' => ['admin'],
        'actionEdit' => ['admin'],
        'actionUpdate' => ['admin'],
        'actionDelete' => ['admin']
    ];

    // Метод для отображения списка пользователей
    public function actionIndex(): string {
        $users = User::getAllUsersFromStorage();
        $render = new Render();

        if (empty($users)) {
            return $render->renderPage(
                'user-empty.tpl',
                [
                    'title' => 'Список пользователей в хранилище',
                    'message' => "Список пуст или не найден"
                ]
            );
        } 

        return $render->renderPage(
            'user-index.tpl',
            [
                'title' => 'Список пользователей в хранилище',
                'users' => $users
            ]
        );
    }

    // Метод для сохранения нового пользователя
    public function actionSave(): string {
        if (!User::validateRequestData()) {
            throw new \Exception("Переданные данные некорректны", 400);
        }

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
    
        $userId = $_POST['user_id'];
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

        $user = User::getUserById($userId);
        if (!$user) {
            throw new \Exception("Пользователь не найден.", 404);
        }

        User::deleteUserById($userId);

        header('Location: /user');
        exit();
    }

    // Метод для авторизации пользователя
    public function actionAuth(): string {
        $render = new Render();
        return $render->renderPageWithForm(
            'user-auth.tpl', 
            [
                'title' => 'Форма логина',
            ]
        );
    }

    // Метод для получения хеша пароля
    public function actionHash(): string {
        if (!isset($_GET['pass_string'])) {
            throw new \Exception("Пароль не передан.", 400);
        }
        return Auth::getPasswordHash($_GET['pass_string']);
    }

    // Метод для логина пользователя
    public function actionLogin(): string {
        if (!isset($_POST['login'], $_POST['password'])) {
            throw new \Exception("Пожалуйста, введите логин и пароль.", 400);
        }

        $result = Application::$auth->proceedAuth($_POST['login'], $_POST['password']);

        if ($result) {
            if (isset($_POST['remember_me'])) {
                setcookie('user_login', $_POST['login'], time() + (86400 * 30), "/");
            }

            header('Location: /');
            return "";
        } 
        
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
