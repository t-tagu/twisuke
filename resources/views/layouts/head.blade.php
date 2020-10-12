<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="description" content="ツイスケはツイッターで繋がっている人とのスケジュール調整を助けるツールです。">
    <meta name="keywords" content="ツイッター,日程調整,スケジュール管理,フォロー,フォロワー,DM">
    <!-- Scripts -->
    <script src="{{ asset('js/bundle.js') }}" defer></script>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css"
    integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt"
    crossorigin="anonymous">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <!-- Styles -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
  </head>
  <body class="l-body">
    <div id="app" class="p-body-inner">
      @yield('content')
    </div>
  </body>
</html>
