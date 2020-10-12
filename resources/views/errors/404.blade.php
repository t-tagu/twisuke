@extends('layouts.head')
@section('content')
<div class="c-container p-container">
  <div class="c-container__inner p-container__inner">
    <form class="c-form p-form">
      @csrf
      <h1 class="p-form__title"><a class="p-form__toplink" href="{{ route('top') }}">ツイスケ</a></h1>
      <p class="p-form__access404">存在しないページへアクセスしようとしました。以下のリンクから遷移して下さい。</p>
      <div class="u-mb30">
        <div class="u-mb15">
          <a class="p-form__link p-form__link--center" href="{{ route('login') }}">{{ __('To Login') }}</a>
        </div>
        <label class="p-form__label p-form__label--left">
          {{ __('Attension') }}
        </label>
      </div>
    </form>
  </div>
</div>
@endsection
