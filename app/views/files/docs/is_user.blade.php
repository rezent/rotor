<?php //show_title('Функция is_user'); ?>

Функция проверят авторизацию пользователя на сайте, возвращает true если пользователь авторизован и false если не авторизован<br />

<pre class="d">
<b>is_user</b>();
</pre><br />

<b>Параметры функции</b><br />
У данной функции нет параметров<br /><br />

<b>Примеры использования</b><br />

<?php
echo App::bbCode(check('[code]<?php
if(is_user()) {
	echo \'Пользователь авторизирован\';
} else {
	echo \'Пользователь не авторизирован\';
}
?>[/code]'));
?>

<br />
<i class="fa fa-arrow-circle-left"></i> <a href="/files/docs">Вернуться</a><br />
