<!DOCTYPE html>
<html>
<head>
    <title>{{ title }}</title>
</head>
<body>
    <h1>{{ title }}</h1>
    <form action="/user/update/" method="post" id="user-edit-form">
        <input id="csrf_token" type="hidden" name="csrf_token" value="{{ csrf_token }}">
        
        <p>
            <label for="user-name">Имя:</label>
            <input id="user-name" type="text" name="name" value="{{ user.getUserName() }}" required>
        </p>
        
        <p>
            <label for="user-lastname">Фамилия:</label>
            <input id="user-lastname" type="text" name="lastname" value="{{ user.getUserLastName() }}" required>
        </p>
        
        <p>
            <label for="user-birthday">День рождения:</label>
            <input id="user-birthday" type="date" name="birthday" value="{{ user.getUserBirthday() | date('Y-m-d') }}" required> <!-- Используем формат YYYY-MM-DD -->
        </p>
        
        <input type="hidden" name="user_id" value="{{ user.idUser }}">
        <p><input type="submit" value="Сохранить"></p>
        
        {% if error_message %}
            <p style="color: red;">{{ error_message }}</p> <!-- Вывод сообщения об ошибке -->
        {% endif %}
    </form>
</body>
</html>
