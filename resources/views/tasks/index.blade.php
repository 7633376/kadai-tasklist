@extends('layouts.app')

@section('content')
    @if (Auth::check())
        
            <aside class="pb-4">
                <h3>『 ユーザ名：{{ Auth::user()->name }} 』のタスク一覧</h3>
            </aside>
            
            <div class="col-sm-12">
                {{-- タスク一覧 --}}
                @include('tasks.list')
            </div>
        
        
        
        
        
    @else
        <div class="center jumbotron">
            <div class="text-center">
                <h1>Welcome to the Tasklist</h1>
                {{-- ユーザ登録ページへのリンク --}}
                {!! link_to_route('signup.get', 'Sign up now!', [], ['class' => 'btn btn-lg btn-primary']) !!}
            </div>
        </div>
    @endif
@endsection
