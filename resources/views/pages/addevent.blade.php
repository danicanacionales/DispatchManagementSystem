@extends('pages.layout')

@section('content')
<script src="https://www.gstatic.com/firebasejs/4.9.1/firebase.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.9.1/firebase-firestore.js"></script>
<script src="{{ asset('js/firestore.js') }}"></script>
<script src="{{ asset('js/addevent.js') }}"></script>

<div class="container col-lg-12">  
    <div class="row">
        {{--  <div class="col-sm-3">
            Summary of Event
        </div>  --}}
        <div class="col-sm-12">            
            <ul class="nav nav-pills nav-justified thumbnail setup-panel" style="padding-bottom: 2%">
                <li class="nav-item active">
                    <a class="nav-link active" href="#step-1">
                        <h5 class="list-group-item-heading">Step 1</h5>
                        <small class="list-group-item-text">Choose type of event</small>
                    </a>
                </li>
                <li class="nav-item disabled">
                    <a class="nav-link" href="#step-2">
                        <h5 class="list-group-item-heading">Step 2</h5>
                        <small class="list-group-item-text">Pin Location</small>
                    </a>
                </li>
                <li class="nav-item disabled">
                    <a class="nav-link" href="#step-3">
                        <h5 class="list-group-item-heading">Step 3</h5>
                        <small class="list-group-item-text">Choose responders</small>
                    </a>
                </li>
                <li class="nav-item disabled">
                    <a class="nav-link" href="#step-4">
                        <h5 class="list-group-item-heading">Step 4</h5>
                        <small class="list-group-item-text">Summary</small>
                    </a>
                </li>
            </ul>

            <div class="row setup-content" id="step-1">
                <div class="col-md-12 well">
                    <h4> What type of event is occuring? </h4>
                    <div class="row card-deck cc-selector">
                        <div class="card">
                            <div class="card-body text-center">
                                <input id="fire" type="radio" name="event-type" value="fire" onclick="AssignType('fire')"/>
                                <label class="drinkcard-cc fire" for="fire"></label>
                                <h6 class="card-title">Fire Incident</h6>
                                {{--  <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>  --}}
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body text-center">
                                <input id="vehicle_accident" type="radio" name="event-type" value="vehicle_accident" onclick="AssignType('vehicle_accident')"/>
                                <label class="drinkcard-cc vehicle_accident"for="vehicle_accident"></label>
                                <h6 class="card-title">Vehicle Accident</h6>
                                {{--  <p class="card-text">This card has supporting text below as a natural lead-in to additional content.</p>  --}}
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body text-center">
                                <input id="medical" type="radio" name="event-type" value="medical" onclick="AssignType('medical')"/>
                                <label class="drinkcard-cc medical" for="medical"></label>
                                <h6 class="card-title">Medical Emergency</h6>
                                {{--  <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This card has even longer content than the first to show that equal height action.</p>  --}}
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger collapse" id="event_alert">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>Alert!</strong> Please choose an event.
                    </div>
                    <div class="row" style="padding-top: 2%;">
                        <div class="col-3">
                            <button id="activate-step-2" type="button" class="btn btn-outline-secondary btn-lg btn-block btn-next">Next</button>  
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="row setup-content" id="step-2">
                <div class="col-md-12 well">
                    <h4>Where is the incident located?</h4>
                    <div class="row">
                        <div class="col-8">
                            <div id="map2" style="height: 100%; margin-top:1%; margin-bottom:1%;"></div>

                            <div class="form-row">
                                <script async defer
                                    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBP5kXEpIl5cRZ7xOc65AIm1NWZt1-WtkQ&libraries=places&callback=initMap">
                                </script>
                            </div>  
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="location-text-box"><b>Enter nearest landmark:</b></label>
                                <input type="text" class="form-control" id="location-text-box" placeholder="i.e. Binan City Hall"/>
                            </div>
                            <div class="form-group">
                                <b><a data-toggle="tooltip" data-placement="right" title="Heads up! You can click on the map to enter the exact location.">Exact Location:</a></b>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <small><label for="lat2">Latitude</label></small>
                                        <input id="lat2" type="text" class="form-control" placeholder="Latitude">
                                    </div>
                                    <div class="col-md-6">
                                        <small><label for="long2">Longitude</label></small>
                                        <input id="long2" type="text" class="form-control" placeholder="Longitude">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button id="activate-step-3" type="button" class="btn btn-default btn-next">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row setup-content" id="step-3">
                <div class="col-md-12 well">
                    <h3>Choose Responders</h3> 
                    <div class="row">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-8">
                                    <div id="map3" style="height: 100%; margin-top:1%; margin-bottom:1%;"></div>
                                </div>
                                <div class="col-md-4">
                                    <ul class="list-group">
                                        <li class="list-group-item list-group-item-info">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="notifyEveryone">
                                                <label class="custom-control-label" for="notifyEveryone">Notify everyone</label>
                                            </div>
                                        </li>
                                    </ul><br/>

                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="cFire">
                                                <label class="custom-control-label" for="cFire">Notify all fire responders</label>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="cMedical">
                                                <label class="custom-control-label" for="cMedical">Notify all medical responders</label>
                                            </div>
                                        </li>
                                    </ul><br/>
                                    <div  style="overflow-y: scroll; height: 550px">
                                        <ul id="responderslist_div" class="list-group">
                                            {{--  this will contain the list of responders' names, distance, availability  --}}
                                        </ul><br/>
                                    </div>
                                    <div class="alert alert-danger collapse" id="responders_alert">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>Alert!</strong> Please choose responders.
                                    </div>

                                    <div class="form-group">
                                        <button id="activate-step-4" type="button" class="btn btn-default btn-next">Next</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row setup-content" id="step-4">
                <div class="col-md-12 well">
                    <h3>Summary</h3> 

                    <div class="row">
                        <div class="col-8">
                            <div id="map4" style="height: 100%; margin-top:1%; margin-bottom:1%;"></div>

                            <div class="form-row">
                                <script async defer
                                    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBP5kXEpIl5cRZ7xOc65AIm1NWZt1-WtkQ&libraries=places&callback=initMap">
                                </script>
                            </div>  
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="inputEvent">Event Type</label>
                                <select id="inputEvent" class="form-control">                                    
                                    <option value="fire">Fire Incident</option>
                                    <option value="vehicle_accident">Vehicular Accident</option>
                                    <option value="medical">Medical Emergency</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="inputLocation">Location</label>
                                <input type="text" class="form-control" id="inputLocation" placeholder="1234 Main St">
                            </div>
                            <div class="form-group">
                                <a data-toggle="tooltip" data-placement="right" title="Heads up! You can click on the map to enter the exact location.">Exact Location:</a>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <small><label for="inputLatitude">Latitude</label></small>
                                        <input id="inputLatitude" type="text" class="form-control" placeholder="Latitude">
                                    </div>
                                    <div class="col-md-6">
                                        <small><label for="inputLongitude">Longitude</label></small>
                                        <input id="inputLongitude" type="text" class="form-control" placeholder="Longitude">
                                    </div>
                                </div>                                
                            </div>
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <p>Responders:</p>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div id="chosenResponders_div" class="list-group col-md-12">
                                    </div>
                                </div>                                
                            </div>
                            <div class="form-row">
                                <div class="alert alert-success collapse" id="successevent_alert">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>Success!</strong> Event has been added.
                                </div>                                
                            </div>
                            <div class="form-group">
                                <button id="submitbtn" type="button" class="btn btn-default btn-next">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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