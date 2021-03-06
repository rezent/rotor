@extends('layout')

@section('title')
    Список новых тем - @parent
@stop

@section('content')
    <h1>Список новых тем</h1>

    <a href="/forum">Форум</a>

    <?php foreach ($topics as $data): ?>
        <div class="b">
            <i class="fa <?=$data->getIcon() ?> text-muted"></i>
            <b><a href="/topic/<?=$data['id']?>"><?=$data['title']?></a></b> (<?=$data['posts']?>)
        </div>

        <div>
            <?= Forum::pagination($data)?>
            Форум: <a href="/forum/<?= $data->getForum()->id ?>"><?= $data->getForum()->title ?></a><br />
            Автор: <?=$data->getUser()->login ?> / Посл.: <?= $data->getLastPost()->getUser()->login ?> (<?=date_fixed($data['created_at'])?>)
        </div>

    <?php endforeach; ?>

    <?php App::pagination($page) ?>
@stop
