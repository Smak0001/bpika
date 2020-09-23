@extends('layouts.page')

@section('article')
    <a class="button is-primary" href="{{ route('users.create') }}">
        <i class="fas fa-user-plus"></i>
    </a>
    <table class="table">
        <thead>
        <tr>
            <th><abbr title="Identifier">ID</abbr></th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('E-Mail Address') }}</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th><abbr title="Identifier">ID</abbr></th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('E-Mail Address') }}</th>
        </tr>
        </tfoot>
        <tbody>
        @foreach($users as $user)
        <tr>
            <th>{{ $user->id }}</th>
            <th>{{ $user->name }}</th>
            <th>{{ $user->email }}</th>
        </tr>
        @endforeach
        </tbody>
    </table>
@endsection
