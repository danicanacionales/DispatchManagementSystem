@extends('auth.layout')

@section('content')
<link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
<style>
    .title {
        font-size: 50px;
        font-family: 'Raleway', sans-serif;
        text-align: center;
        color: #fff;
    }
</style>

<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
<script src="https://www.gstatic.com/firebasejs/4.9.1/firebase.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.9.1/firebase-firestore.js"></script>
<script src="{{ asset('js/firestore.js') }}"></script>
{{-- <script src="{{ asset('js/register.js') }}"></script> --}}

<div class="container">
    <div class="title m-b-md">
        DSPTCH
    </div>
    <div class="card card-register mx-auto mt-5">
        <div class="card-header">Register an Account</div>
        <div class="card-body">
            {!! Form::open(['action' => 'LoginController@create', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                <div class="form-group">
                    <label for="username" class="control-label">Username</label>
                    {{Form::text('username', '', ['class' => 'form-control', 'placeholder' => 'Name'])}}                    
                </div>

                <div class="form-group">                    
                    <label for="user_type" class="col-md-4 control-label">User Type</label>
                    {{ Form::select('user_type', ['Command Center Officer' => 'Command Center Officer', 'Fire Responder' => 'Fire Responder', 'Medical Responder' => 'Medical Responder'], null, ['class' => 'form-control']) }}                    
                </div>
            
                <div class="form-group">
                    <div class="form-row">
                        <div class="col-md-6">
                            <label for="exampleInputName">First name</label>
                            <input class="form-control" id="exampleInputName" type="text" aria-describedby="nameHelp" placeholder="Enter first name">
                        </div>
                        <div class="col-md-6">
                            <label for="exampleInputLastName">Last name</label>
                            <input class="form-control" id="exampleInputLastName" type="text" aria-describedby="nameHelp" placeholder="Enter last name">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-row">
                        <div class="col-md-6">
                            <label for="password">Password</label>
                            {{ Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password']) }}
                        </div>
                        <div class="col-md-6">
                            <label for="exampleConfirmPassword">Confirm password</label>
                            <input class="form-control" id="exampleConfirmPassword" type="password" placeholder="Confirm password">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" onclick="upload()">Register</button>
                {{--  <a class="btn btn-primary btn-block" href="{{ url('/register') }}">Register</a>  --}}

                <div class="text-center">
                    <a class="d-block small mt-3" href="{{ url('/') }}">Login Page</a>
                    <a class="d-block small" href="#">Forgot Password?</a>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection