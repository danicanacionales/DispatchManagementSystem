<head>
    <style>
        .fire{background-image:url('{{ asset('img/fire.png') }}');}
        .vehicle_accident{background-image:url('{{ asset('img/vehicle_accident.png') }}');}
        .medical{background-image:url('{{ asset('img/medical.png') }}');}
    </style>
</head>

@if(session()->exists('username'))
<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href="{{ url('/home') }}">DSPTCH</a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard">
            <a class="nav-link" href="{{ url('/home') }}">
            <i class="fa fa-fw fa-dashboard"></i>
            <span class="nav-link-text">Dashboard</span>
            </a>
        </li>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Ongoing Incidents">
            <a class="nav-link" href="{{ url('/ongoing') }}">
            <i class="fa fa-fw fa-warning"></i>
            <span class="nav-link-text">Ongoing Incidents</span>
            </a>
        </li>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Past Incidents">
            <a class="nav-link" href="{{ url('/pastincidents') }}">
            <i class="fa fa-fw fa-check"></i>
            <span class="nav-link-text">Past Incidents</span>
            </a>
        </li>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Responders' Location">
            <a class="nav-link" href="{{ url('/map') }}">
            <i class="fa fa-fw fa-map-marker"></i>
            <span class="nav-link-text">Responders' Location</span>
            </a>
        </li>
        </ul>
        <ul class="navbar-nav sidenav-toggler">
        <li class="nav-item">
            <a class="nav-link text-center" id="sidenavToggler">
            <i class="fa fa-fw fa-angle-left"></i>
            </a>
        </li>
        </ul>
        <ul class="navbar-nav ml-auto">
        <li class="nav-item">
        <a class="nav-link" href="{{url('addevent')}}">
            <i class="fa fa-fw fa-plus"></i>Add Incident</a>
        </li>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/userprofile') }}">
                <i class="fa fa-fw fa-user"></i>{{ $user_details["name"] }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/logout') }}">
                <i class="fa fa-fw fa-sign-out"></i>Logout
            </a>
        </li>
        </ul>
    </div>
</nav>













    <script>
        function showModal(id) {
            if(id == 'myModal4')
                document.getElementById('myModal4').value = event_summary.type;            

            $(".modal").modal('hide');
            $(".modal-backdrop").remove();
            $("#" + id).modal();
        }    
    </script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

{{-- <script src="https://www.gstatic.com/firebasejs/4.9.1/firebase.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.9.1/firebase-firestore.js"></script>
<script src="{{ asset('js/firestore.js') }}"></script> --}}

@endif