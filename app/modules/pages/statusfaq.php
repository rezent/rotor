<?php
App::view(App::setting('themes').'/index');

//show_title('Статусы пользователей');

echo 'В зависимости от вашей активности на сайте вы получаете определенный статус<br />';
echo 'При наборе определенного количества актива ваш статус меняется на вышестоящий<br />';
echo 'Актив - это сумма постов на форуме, гостевой, в комментариях и пр.<br /><br />';


$querystatus = DB::run()->query("SELECT * FROM `status` ORDER BY `topoint` DESC;");
$status = $querystatus->fetchAll();
$total = count($status);

if ($total>0){
    foreach ($status as $statval){

        echo '<i class="fa fa-user-circle-o"></i> ';

        if (empty($statval['color'])){
            echo '<b>'.$statval['name'].'</b> — '.points($statval['topoint']).'<br />';
        } else {
            echo '<b><span style="color:'.$statval['color'].'">'.$statval['name'].'</span></b> — '.points($statval['topoint']).'<br />';
        }
    }

    echo '<br />';
} else {
    show_error('Статусы еще не назначены!');
}

echo 'Некоторые статусы могут быть выделены определенными цветами<br />';
echo 'Самым активным юзерам администрация сайта может назначать особые статусы<br /><br />';

App::view(App::setting('themes').'/foot');
