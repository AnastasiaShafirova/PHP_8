<p>Список пользователей в хранилище</p>

<ul id="navigation">
    {% for user in users %}
        <li>
            {{ user.getUserName() }} {{ user.getUserLastName() }}. День рождения: {{ user.getUserBirthday() | date('d.m.Y') }}
            <a href="/user/edit/?user_id={{ user.idUser }}">Править</a>
            <a href="/user/delete/?user_id={{ user.idUser }}">Удалить</a>
        </li>
    {% endfor %}
</ul>

