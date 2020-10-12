@extends('layouts.head')
@section('content')
  <div class="c-container p-container">
    <div class="c-container__inner p-container__inner">
      <form class="c-form p-form" method="POST" action="{{ route('login.twitter') }}">
        @csrf
        <h1 class="p-form__title"><a class="p-form__toplink">ツイスケ</a></h1>
        <h2 class="p-form__subtitle">ログイン</h2>
        <div class="c-form__box p-form__box u-mb25">
          <button type="submit" class="c-form__btn p-form__btn">
            <i class="fab fa-twitter p-form__icon"></i>{{ __('Login With Twitter Account') }}
          </button>
        </div>
        <div class="c-form__box p-form__box">
          <input class="p-form__check" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
          <label class="p-form__label" for="remember">
            {{ __('Remember Me') }}
          </label>
        </div>
        <div class="p-form__linkbox">
          <a class="p-form__link p-form__link--top" href="{{ route('top') }}">{{ __('Click Here To Top') }}</a>
        </div>
      </form>
    </div>
  </div>
@endsection
