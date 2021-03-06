<?php
App::view(App::setting('themes').'/index');

if (isset($_GET['act'])) {
    $act = check($_GET['act']);
} else {
    $act = 'index';
}
if (isset($_GET['uz'])) {
    $uz = check($_GET['uz']);
} elseif (isset($_POST['uz'])) {
    $uz = check($_POST['uz']);
} else {
    $uz = "";
}
$page = abs(intval(Request::input('page', 1)));

//show_title('Рейтинг репутации');

switch ($act):
############################################################################################
##                                    Вывод пользователей                                 ##
############################################################################################
    case 'index':

        $total = DB::run() -> querySingle("SELECT count(*) FROM `users`;");
        $page = App::paginate(App::setting('avtorlist'), $total);

        if ($total > 0) {

            $queryusers = DB::run() -> query("SELECT * FROM `users` ORDER BY `rating` DESC, `login` ASC LIMIT ".$page['offset'].", ".App::setting('avtorlist').";");

            $i = 0;
            while ($data = $queryusers -> fetch()) {
                ++$i;

                echo '<div class="b">'.($page['offset'] + $i).'. '.user_gender($data['login']);

                if ($uz == $data['login']) {
                    echo ' <b><big>'.profile($data['login'], '#ff0000').'</big></b> (Репутация: '.($data['rating']).')</div>';
                } else {
                    echo ' <b>'.profile($data['login']).'</b> (Репутация: '.($data['rating']).')</div>';
                }

                echo '<div>Плюсов: '.$data['posrating'].' / Минусов: '.$data['negrating'].'<br />';
                echo 'Дата регистрации: '.date_fixed($data['joined'], 'j F Y').'</div>';
            }

            App::pagination($page);

            echo '<div class="form">';
            echo '<b>Поиск пользователя:</b><br />';
            echo '<form action="/authoritylist?act=search&amp;page='.$page['current'].'" method="post">';
            echo '<input type="text" name="uz" value="'.App::getUsername().'" />';
            echo '<input type="submit" value="Искать" /></form></div><br />';

            echo 'Всего пользователей: <b>'.$total.'</b><br /><br />';
        } else {
            show_error('Пользователей еще нет!');
        }
    break;

    ############################################################################################
    ##                                  Поиск пользователя                                    ##
    ############################################################################################
    case 'search':

        if (!empty($uz)) {
            $queryuser = DB::run() -> querySingle("SELECT `login` FROM `users` WHERE LOWER(`login`)=? LIMIT 1;", [strtolower($uz)]);

            if (!empty($queryuser)) {
                $queryrating = DB::run() -> query("SELECT `login` FROM `users` ORDER BY `rating` DESC, `login` ASC;");
                $ratusers = $queryrating -> fetchAll(PDO::FETCH_COLUMN);

                foreach ($ratusers as $key => $ratval) {
                    if ($queryuser == $ratval) {
                        $rat = $key + 1;
                    }
                }
                if (!empty($rat)) {
                    $end = ceil($rat / App::setting('avtorlist'));

                    App::setFlash('success', 'Позиция в рейтинге: '.$rat);
                    App::redirect("/authoritylist?page=$end&uz=$queryuser");
                } else {
                    show_error('Пользователь с данным логином не найден!');
                }
            } else {
                show_error('Пользователь с данным логином не зарегистрирован!');
            }
        } else {
            show_error('Ошибка! Вы не ввели логин пользователя');
        }

        echo '<i class="fa fa-arrow-circle-left"></i> <a href="/authoritylist?page='.$page.'">Вернуться</a><br />';
    break;

endswitch;

App::view(App::setting('themes').'/foot');
