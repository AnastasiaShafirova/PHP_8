{% if not auth-success %}
  {{ auth-error }}
{% endif %}


<form action="/user/login/" method="post">
    <input id="csrf_token" type="hidden" name="csrf_token" value="{{ csrf_token }}">
    <p>
        <label for="user-login">Логин:</label>
        <input id="user-login" type="text" name="login" required>
    </p>
    <p>
        <label for="user-password">Пароль:</label>
        <input id="user-password" type="password" name="password" required>
    </p>
    <p>
        <input type="checkbox" id="remember-me" name="remember_me">
        <label for="remember-me">Запомнить меня</label>
    </p>
    <p><input type="submit" value="Войти"></p>
</form>

<script>
    window.onload = function() {
        var userLogin = getCookie("user_login");
        if (userLogin) {
            document.getElementById("user-login").value = userLogin;
        }
    };

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }
</script>
