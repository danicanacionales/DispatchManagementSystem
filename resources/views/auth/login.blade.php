@extends('auth.layout')

@section('content')
<link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

<style>
    .title {
        font-size: 84px;
        font-family: 'Raleway', sans-serif;
        text-align: center;
        color: #fff;
    }
</style>

<div class="container">
    <div class="title m-b-md">
        DSPTCH
    </div>
    <div class="card card-login mx-auto mt-5">
        <div class="card-header">Login</div>
        <div class="card-body">
        <form class="form-horizontal" method="GET" action="{{ url('/login?username=&password=') }}">
            {{ csrf_field() }}
            <div class="form-group">
            <label for="email">Username</label>
            <input class="form-control" id="email" name="user" type="text" aria-describedby="emailHelp" placeholder="Username">
            </div>
            <div class="form-group">
            <label for="password">Password</label>
            <input class="form-control" name="pass" id="password" type="password" placeholder="Password">
            </div>
            <div class="form-group">
            <div class="form-check">
                <label class="form-check-label">
                <input class="form-check-input" type="checkbox"> Remember Password</label>
            </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </div>
        </form>
        <div class="text-center">
            <a class="d-block small mt-3" href="{{ url('/register') }}">Register an Account</a>
            <a class="d-block small">Forgot Password?</a>
        </div>
        </div>
    </div>
</div>

@endsection