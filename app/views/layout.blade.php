@include(App::setting('themes').'.index' )

<div style="text-align:center">
    @include('advert.top_all')

    <?= show_advertuser(); ?>
</div>

{{ App::getFlash() }}

@yield('content')

@include('advert.bottom_all')
@include(App::setting('themes').'.foot')
