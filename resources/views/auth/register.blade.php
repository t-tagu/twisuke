@extends('layouts.head')

@section('content')
  <div class="c-container p-container">
    <div class="c-container__inner p-container__inner">
      <form class="c-form p-form" method="GET" action="{{ route('register.twitter') }}">
        @csrf
        <h1 class="p-form__title"><a class="p-form__toplink">ツイスケ</a></h1>
        <h2 class="p-form__subtitle">新規登録</h2>
        <div class="c-form__box">
          <button type="submit" class="c-form__btn p-form__btn">
            <i class="fab fa-twitter p-form__icon"></i>{{ __('Register With Twitter Account') }}
          </button>
          <div class="p-form__error">
            
          </div>
        </div>
        <div class="p-form__linkbox">
          <a class="p-form__link p-form__link--top" href="{{ route('login') }}">{{ __('If You Have Your Account') }}</a>
        </div>
      </form>
    <div>
  </div>
@endsection
