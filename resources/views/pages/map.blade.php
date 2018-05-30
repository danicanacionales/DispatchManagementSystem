@extends('pages.layout')

<script src="https://www.gstatic.com/firebasejs/4.9.1/firebase.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.9.1/firebase-firestore.js"></script>
<script src="{{ asset('js/firestore.js') }}"></script>

@section('content')
<script>
    var data = {
        lat: null,
        lng: null,
        name: null
    };
    var token ='{{ session() -> get('token')}}';

    firebase.auth().signInWithCustomToken(token).catch(function(error) {
        // Debug
        var errorCode = error.code;
        var errorMessage = error.message;
        // ...
        console.log(errorCode + " " + errorMessage);
    });


</script>

<div class="container col-lg-12">  
    <div class="row">
        <div class="contacts col-sm-3">
            <div class="row">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li id="respondersTab" class="nav-item">
                        <a class="nav-link active" id="responders-tab" data-toggle="tab" href="#responders" role="tab" aria-controls="responders" aria-selected="true">Responders</a>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="responders" role="tabpanel" aria-labelledby="responders-tab">
                    <div  style="overflow-y: auto; height: 650px">
                        <div id="responders_div" class="list-group">
                            {{--  list of responders here  --}}
                        </div>
                    </div>
                    
                </div>

                <div class="tab-pane fade" id="events" role="tabpanel" aria-labelledby="events-tab">
                    Events
                </div>
            </div>
        </div>
        <div class="col-sm-9">
            <h4>Locations</h4>
            <input type="text" class="form-control" id="location-text-box1" />
            <div id="map1" style="height: 80%; margin-top:1%"></div>

            <script async defer
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBP5kXEpIl5cRZ7xOc65AIm1NWZt1-WtkQ&libraries=places&callback=initMap">
            </script>
        </div>
    </div>
</div>
@endsection

@section('page-js-files')
    <!-- Scripts -->
    <script src="{{ asset('js/includes/app.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="{{ asset('js/includes/sb-admin.min.js') }}"></script>
    <script src="{{ asset('js/includes/navbar.js') }}"></script>
@stop

@section('page-js-script')
@stop

<script src="{{ asset('js/map.js') }}"></script>