<?php

/**
 * Переадресация старых ссылок
 *
 * Все редиректы постоянные - 301 Moved Permanently
 * Необходимо чтобы поисковики не выкинули страницы из поиска
 * Если у вас новый проект, то эту часть можно смело вырезать
 */

if ($_SERVER['REQUEST_URI']) {

    $parse = parse_url($_SERVER['REQUEST_URI']);

    if (isset($parse['path'])) {
        if (strpos($parse['path'], '/upload/') !== false) {
            $parse['path'] = str_replace('/upload/', '/uploads/', $parse['path']);
            App::redirect($parse['path'], true);
        }
    }

    if (isset($parse['path']) && $parse['path'] == '/news/rss.php'){
        App::redirect('/news/rss', true);
    }

    if (isset($parse['path']) && ($parse['path'] == '/services/' || $parse['path'] == '/services')){
        App::redirect('/files', true);
    }

    if (isset($parse['path']) && isset($parse['query'])) {

        parse_str($parse['query'], $output);

        if ($parse['path'] == '/forum/forum.php' && isset($output['fid']) && is_numeric($output['fid'])){
            App::redirect('/forum/'.$output['fid'], true);
        }

        if ($parse['path'] == '/forum/topic.php' && isset($output['tid']) && is_numeric($output['tid'])){
            App::redirect('/topic/'.$output['tid'], true);
        }

        if ($parse['path'] == '/forum/print.php' && isset($output['tid']) && is_numeric($output['tid'])){
            App::redirect('/topic/'.$output['tid'].'/print', true);
        }

        if ($parse['path'] == '/forum/rss.php' && isset($output['tid']) && is_numeric($output['tid'])){
            App::redirect('/topic/'.$output['tid'].'/rss', true);
        }

        if ($parse['path'] == '/blog/print.php' && isset($output['id']) && is_numeric($output['id'])){
            App::redirect('/blog/print?id='.$output['id'], true);
        }

        if (
            $parse['path'] == '/forum/active.php' &&
            isset($output['act']) &&
            isset($output['uz'])
        ){
            App::redirect('/forum/active/'.$output['act'].'?user='.$output['uz'], true);
        }

        if (
            $parse['path'] == '/load/active.php' &&
            isset($output['act']) &&
            isset($output['uz'])
        ){
            App::redirect('/load/active?act='.$output['act'].'&uz='.$output['uz'], true);
        }

        if (
            $parse['path'] == '/blog/active.php' &&
            isset($output['act']) &&
            isset($output['uz'])
        ){
            App::redirect('/blog/active?act='.$output['act'].'&uz='.$output['uz'], true);
        }

        if (
            $parse['path'] == '/blog/blog.php' &&
            isset($output['act']) &&
            $output['act'] == 'view' &&
            isset($output['id']) &&
            is_numeric($output['id'])
        ){
            App::redirect('/blog/blog?act=view&id='.$output['id'], true);
        }

        if (
            $parse['path'] == '/load/zip.php' &&
            isset($output['act']) &&
            $output['act'] == 'preview' &&
            isset($output['id']) &&
            is_numeric($output['id']) &&
            isset($output['view']) &&
            isset($output['img'])
        ){
            App::redirect('/load/zip?act=preview&id='.$output['id'].'&view='.$output['view'].'&img=1', true);
        }

        if (
            $parse['path'] == '/load/zip.php' &&
            isset($output['act']) &&
            $output['act'] == 'preview' &&
            isset($output['id']) &&
            is_numeric($output['id']) &&
            isset($output['view']) &&
            is_numeric($output['view'])
        ){
            App::redirect('/load/zip?act=preview&id='.$output['id'].'&view='.$output['view'], true);
        }

        if ($parse['path'] == '/load/zip.php' && isset($output['id']) && is_numeric($output['id'])){
            App::redirect('/load/zip?id='.$output['id'], true);
        }


        if (
            $parse['path'] == '/load/down.php' &&
            isset($output['act']) &&
            isset($output['id']) &&
            is_numeric($output['id'])
        ){
            App::redirect('/load/down?act='.$output['act'].'&id='.$output['id'], true);
        }

        if ($parse['path'] == '/gallery/index.php' && isset($output['act']) &&
            $output['act'] == 'view' && isset($output['gid']) && is_numeric($output['gid'])){
            App::redirect('/gallery?act=view&gid='.$output['gid'], true);
        }

        if ($parse['path'] == '/gallery/album.php' && isset($output['act']) &&
            $output['act'] == 'photo' && isset($output['uz'])){
            App::redirect('/gallery/album?act=photo&uz='.$output['uz'], true);
        }

        if ($parse['path'] == '/gallery/comments.php' && isset($output['act']) &&
            $output['act'] == 'comments' && isset($output['uz'])){
            App::redirect('/gallery/comments?act=comments&uz='.$output['uz'], true);
        }

        if ($parse['path'] == '/pages/wall.php' && isset($output['uz'])){
            App::redirect('/wall?uz='.$output['uz'], true);
        }

        if ($parse['path'] == '/pages/user.php' && isset($output['uz'])){
            App::redirect('/user/'.$output['uz'], true);
        }
    }

}
