<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>DSPTCH</title>

    <!-- Styles -->
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="{{ asset('js/vendor/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">
    
    <link href="{{ asset('css/sb-admin.css') }}" rel="stylesheet">
</head>

<body class="fixed-nav bg-dark" id="page-top">
    @include('pages.navbar')
    <div class="content-wrapper">
        <div class="container-fluid" id="page-top">
            <div class="col-sm-9 col-lg-12">
                @yield('content')
            </div>
        </div>
    </div>        

    
</body>

@yield('page-js-files')
@yield('page-js-script')

</html>
