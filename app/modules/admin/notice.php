<?php
App::view(App::setting('themes').'/index');

$act = (isset($_GET['act'])) ? check($_GET['act']) : 'index';
$id = (isset($_GET['id'])) ? abs(intval($_GET['id'])) : 0;

if (! is_admin([101])) App::redirect('/admin/');

//show_title('Шаблоны писем');

switch ($act):

/**
 * Главная страница
 */
case 'index':

    $total = Notice::count();

    if ($total > 0) {

        $notices = Notice::orderBy('id')
            ->with('user')
            ->get();

        foreach ($notices as $notice) {

            echo '<div class="b">';

            echo '<i class="fa fa-envelope"></i> <b><a href="/admin/notice?act=edit&amp;id='.$notice['id'].'">'.$notice['name'].'</a></b>';
            if (empty($notice['protect'])) {
                echo ' (<a href="/admin/notice?act=del&amp;id='.$notice['id'].'&amp;token='.$_SESSION['token'].'">Удалить</a>)';
            } else {
                echo ' (Системный шаблон)';
            }
            echo '</div>';

            echo '<div>Изменено: ';

            echo profile($notice['user']);

            echo ' ('.date_fixed($notice['updated_at']).')';

            echo '</div>';
        }

        echo '<br />Всего шаблонов: '.$total.'<br /><br />';

    } else {
        show_error('Шаблонов еще нет!');
    }
    echo '<i class="fa fa-check"></i> <a href="/admin/notice?act=new">Добавить</a><br />';
break;

/**
 * Coздание шаблона
 */
case 'new':
    //show_title('Новый шаблон');

    echo '<div class="form">';
    echo '<form action="/admin/notice?act=save" method="post">';
    echo '<input type="hidden" name="token" value="'.$_SESSION['token'].'">';
    echo 'Название: <br />';
    echo '<input type="text" name="name" maxlength="100" size="50" /><br />';
    echo '<textarea id="markItUp" cols="35" rows="20" name="text"></textarea><br />';
    echo '<input name="protect" id="protect" type="checkbox" value="1" /> <label for="protect">Системный шаблон</label><br />';

    echo '<input type="submit" value="Сохранить" /></form></div><br />';

    App::view('includes/back', ['link' => '/admin/notice', 'title' => 'Вернуться']);
break;

/**
 * Редактирование шаблона
 */
case 'edit':
    $notice = Notice::find($id);
    if ($notice) {

        if ($notice['protect']) {
            echo '<div class="info"><i class="fa fa-exclamation-circle"></i> <b>Вы редактируете системный шаблон</b></div><br />';
        }

        echo '<div class="form">';
        echo '<form action="/admin/notice?act=save&amp;id='.$id.'" method="post">';
        echo '<input type="hidden" name="token" value="'.$_SESSION['token'].'">';
        echo 'Название: <br />';
        echo '<input type="text" name="name" maxlength="100" size="50" value="'.$notice['name'].'" /><br />';
        echo '<textarea id="markItUp" cols="35" rows="20" name="text">'.$notice['text'].'</textarea><br />';

        $checked = $notice['protect'] ? ' checked="checked"' : '';

        echo '<input name="protect" id="protect" type="checkbox" value="1" '.$checked.' /> <label for="protect">Системный шаблон</label><br />';

        echo '<input type="submit" value="Изменить" /></form></div><br />';

    } else {
        show_error('Ошибка! Шаблона для редактирования не существует!');
    }

    App::view('includes/back', ['link' => '/admin/notice', 'title' => 'Вернуться']);
break;

/**
 * Сохранение шаблона
 */
case 'save':

    $token = check(Request::input('token'));
    $name = isset($_POST['name']) ? check($_POST['name']) : '';
    $text = isset($_POST['text']) ? check($_POST['text']) : '';
    $protect = ! empty($_POST['protect']) ? 1 : 0;

    $validation = new Validation();

    $validation -> addRule('equal', [$token, $_SESSION['token']], 'Неверный идентификатор сессии, повторите действие!')
        -> addRule('string', $name, 'Слишком длинный или короткий заголовок шаблона!', true, 5, 100)
        -> addRule('string', $text, 'Слишком длинный или короткий текст шаблона!', true, 10, 65000);

    if ($validation->run()) {

        $note = [
            'name'       => $name,
            'text'       => $text,
            'user_id'    => App::getUserId(),
            'protect'    => $protect,
            'created_at' => SITETIME,
            'updated_at' => SITETIME,
        ];

        $notice = Notice::updateOrCreate(
            ['id' => $id],
            $note
        );

        App::setFlash('success', 'Шаблон успешно сохранен!');
        App::redirect('/admin/notice?act=edit&id='.$notice->id);

    } else {
        show_error($validation->getErrors());
    }

    App::view('includes/back', ['link' => '/admin/notice?act=edit&amp;id='.$id, 'title' => 'Вернуться']);
break;

/**
 * Удаление шаблона
 */
case 'del':

    $token = check(Request::input('token'));

    $notice = Notice::find($id);

    $validation = new Validation();

    $validation -> addRule('equal', [$token, $_SESSION['token']], 'Неверный идентификатор сессии, повторите действие!')
        -> addRule('not_empty', $notice, 'Не найден шаблон для удаления!')
        -> addRule('empty', $notice['protect'], 'Запрещено удалять защищенный шаблон!');

    if ($validation->run()) {

        $notice->delete();

        App::setFlash('success', 'Выбранный шаблон успешно удален!');
        App::redirect('/admin/notice');

    } else {
        show_error($validation->getErrors());
    }

    App::view('includes/back', ['link' => '/admin/notice', 'title' => 'Вернуться']);
break;

endswitch;

App::view('includes/back', ['link' => '/admin', 'title' => 'В админку', 'icon' => 'fa-wrench']);

App::view(App::setting('themes').'/foot');
