@extends('layouts.app')

@section('title', 'ダッシュボード')

@section('content')
    <h1>ダッシュボード</h1>

    <p>ようこそ！ <a href="{{ route('profile') }}">{{ auth()->user()->username }}</a></p>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">ログアウト</button>
    </form>
@endsection
