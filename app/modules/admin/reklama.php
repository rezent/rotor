<?php
App::view(App::setting('themes').'/index');

if (isset($_GET['act'])) {
    $act = check($_GET['act']);
} else {
    $act = 'index';
}
$page = abs(intval(Request::input('page', 1)));

if (is_admin()) {
    //show_title('Пользовательская реклама');

    switch ($act):
    ############################################################################################
    ##                                    Главная страница                                    ##
    ############################################################################################
        case 'index':

            $total = DB::run() -> querySingle("SELECT count(*) FROM `rekuser` WHERE `time`>?;", [SITETIME]);
            $page = App::paginate(App::setting('rekuserpost'), $total);

            if ($total > 0) {

                $queryrek = DB::run() -> query("SELECT * FROM `rekuser` WHERE `time`>? ORDER BY `time` DESC LIMIT ".$page['offset'].", ".App::setting('rekuserpost').";", [SITETIME]);

                echo '<form action="/admin/reklama?act=del&amp;page='.$page['current'].'&amp;uid='.$_SESSION['token'].'" method="post">';

                while ($data = $queryrek -> fetch()) {
                    echo '<div class="b">';
                    echo '<i class="fa fa-check-circle"></i> ';
                    echo '<b><a href="'.$data['site'].'">'.$data['name'].'</a></b> ('.profile($data['user']).')<br />';

                    echo '<input type="checkbox" name="del[]" value="'.$data['id'].'" /> ';
                    echo '<a href="/admin/reklama?act=edit&amp;id='.$data['id'].'&amp;page='.$page['current'].'">Редактировать</a>';
                    echo '</div>';

                    echo 'Истекает: '.date_fixed($data['time']).'<br />';

                    if (!empty($data['color'])) {
                        echo 'Цвет: <span style="color:'.$data['color'].'">'.$data['color'].'</span>, ';
                    } else {
                        echo 'Цвет: нет, ';
                    }

                    if (!empty($data['bold'])) {
                        echo 'Жирность: есть<br />';
                    } else {
                        echo 'Жирность: нет<br />';
                    }
                }
                echo '<br /><input type="submit" value="Удалить выбранное" /></form>';

                App::pagination($page);

                echo 'Всего ссылок: <b>'.$total.'</b><br /><br />';
            } else {
                show_error('В данный момент рекламных ссылок еще нет!');
            }
        break;

        ############################################################################################
        ##                               Подготовка к редактированию                              ##
        ############################################################################################
        case 'edit':

            //App::setting('newtitle') = 'Редактирование ссылки';

            $id = abs(intval($_GET['id']));

            $data = DB::run() -> queryFetch("SELECT * FROM `rekuser` WHERE `id`=? LIMIT 1;", [$id]);

            if (!empty($data)) {
                echo '<b><big>Редактирование заголовка</big></b><br /><br />';

                echo '<div class="form">';
                echo '<form action="/admin/reklama?act=change&amp;id='.$id.'&amp;page='.$page.'&amp;uid='.$_SESSION['token'].'" method="post">';
                echo 'Адрес сайта:<br />';
                echo '<input name="site" type="text" value="'.$data['site'].'" maxlength="50" /><br />';

                echo 'Название ссылки:<br />';
                echo '<input name="name" type="text" maxlength="35" value="'.$data['name'].'" /><br />';

                echo 'Код цвета:';

                if (file_exists(BASEDIR.'/modules/services/colors.php')) {
                    echo ' <a href="/services/colors">(?)</a>';
                }

                echo '<br />';
                echo '<input name="color" type="text" maxlength="7" value="'.$data['color'].'" /><br />';

                echo 'Жирность: ';
                $checked = ($data['bold'] == 1) ? ' checked="checked"' : '';
                echo '<input name="bold" type="checkbox" value="1"'.$checked.' /><br />';

                echo '<br /><input type="submit" value="Изменить" /></form></div><br />';
            } else {
                show_error('Ошибка! Данной ссылки не существует!');
            }

            echo '<i class="fa fa-arrow-circle-left"></i> <a href="/admin/reklama?page='.$page.'">Вернуться</a><br />';
        break;

        ############################################################################################
        ##                                     Редактирование                                     ##
        ############################################################################################
        case 'change':

            $id = abs(intval($_GET['id']));

            $uid = check($_GET['uid']);
            $site = check($_POST['site']);
            $name = check($_POST['name']);
            $color = check($_POST['color']);
            $bold = (empty($_POST['bold'])) ? 0 : 1;

            if ($uid == $_SESSION['token']) {
                if (preg_match('#^https?://([а-яa-z0-9_\-\.])+(\.([а-яa-z0-9\/\-?_=])+)+$#iu', $site)) {
                    if (utf_strlen($site) >= 5 && utf_strlen($site) <= 50) {
                        if (utf_strlen($name) >= 5 && utf_strlen($name) <= 35) {
                            if (preg_match('|^#+[A-f0-9]{6}$|', $color) || empty($color)) {
                                $data = DB::run() -> queryFetch("SELECT * FROM `rekuser` WHERE `id`=? LIMIT 1;", [$id]);
                                if (!empty($data)) {
                                    DB::run() -> query("UPDATE `rekuser` SET `site`=?, `name`=?, `color`=?, `bold`=? WHERE `id`=?", [$site, $name, $color, $bold, $id]);
                                    save_advertuser();

                                    App::setFlash('success', 'Рекламная ссылка успешно изменена!');
                                    App::redirect("/admin/reklama?page=$page");
                                } else {
                                    show_error('Ошибка! Редактируемой ссылки не существует!');
                                }
                            } else {
                                show_error('Ошибка! Недопустимый формат цвета ссылки! (пример #ff0000)');
                            }
                        } else {
                            show_error('Ошибка! Слишком длинное или короткое название ссылки! (от 5 до 35 символов)');
                        }
                    } else {
                        show_error('Ошибка! Слишком длинный или короткий адрес ссылки! (от 5 до 50 символов)');
                    }
                } else {
                    show_error('Ошибка! Недопустимый адрес сайта!');
                }
            } else {
                show_error('Ошибка! Неверный идентификатор сессии, повторите действие!!');
            }

            echo '<i class="fa fa-arrow-circle-left"></i> <a href="/admin/reklama?act=edit&amp;id='.$id.'&amp;page='.$page.'">Вернуться</a><br />';
        break;

        ############################################################################################
        ##                                   Удаление ссылок                                      ##
        ############################################################################################
        case 'del':

            $uid = check($_GET['uid']);
            if (isset($_POST['del'])) {
                $del = intar($_POST['del']);
            } else {
                $del = 0;
            }

            if ($uid == $_SESSION['token']) {
                if (!empty($del)) {
                    $del = implode(',', $del);

                    DB::run() -> query("DELETE FROM `rekuser` WHERE `id` IN (".$del.");");
                    save_advertuser();

                    App::setFlash('success', 'Выбранные ссылки успешно удалены!');
                    App::redirect("/admin/reklama?page=$page");
                } else {
                    show_error('Ошибка! Не выбраны ссылки для удаления!');
                }
            } else {
                show_error('Ошибка! Неверный идентификатор сессии, повторите действие!');
            }

            echo '<i class="fa fa-arrow-circle-left"></i> <a href="/admin/reklama?page='.$page.'">Вернуться</a><br />';
        break;

    endswitch;

    echo '<i class="fa fa-wrench"></i> <a href="/admin">В админку</a><br />';

} else {
    App::redirect("/");
}

App::view(App::setting('themes').'/foot');
