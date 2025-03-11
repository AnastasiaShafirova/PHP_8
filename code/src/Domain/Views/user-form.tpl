<!DOCTYPE html>
<html>
<head>
    <title>{{ title }}</title>
</head>
<body>
<form action="/user/update/" method="post">
  <input id="csrf_token" type="hidden" name="csrf_token" value="{{ csrf_token }}">
  
  <p>
    <label for="user-name">Имя:</label>
    <input id="user-name" type="text" name="name" value="{{ user.getUserName() }}">
  </p>
  <p>
    <label for="user-lastname">Фамилия:</label>
    <input id="user-lastname" type="text" name="lastname" value="{{ user.getUserLastName() }}">
  </p>
  <p>
    <label for="user-birthday">День рождения:</label>
    <input id="user-birthday" type="text" name="birthday" value="{{ user.getUserBirthday() | date('d.m.Y') }}">
  </p>
  <input type="hidden" name="user_id" value="{{ user.idUser }}"> <!-- Не забудьте передать user_id -->
  <p><input type="submit" value="Сохранить"></p>
</form>
</body>
</html>
