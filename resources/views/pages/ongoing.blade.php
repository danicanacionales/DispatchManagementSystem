@extends('pages.layout')

<script src="https://www.gstatic.com/firebasejs/4.9.1/firebase.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.9.1/firebase-firestore.js"></script>
<script src="{{ asset('js/firestore.js') }}"></script>

@section('content')
    
    <script src="{{ asset('js/chat.js') }}"></script>
    <link href="{{ asset('css/chat.css') }}" rel="stylesheet">

    <script>
        $(document).on("click", ".reportperson", function(){
            
            var report_id = jQuery(this).attr("id");
            var report_name = jQuery(this).attr("name");
            
            $.get("{{ url('/sendevent') }}", function(data){
                if(report_name == "incident_reports"){
                    ReturnReportDetails(report_id);
                    document.getElementById('patientAssessmentDiv').style.display = "none";
                    document.getElementById('incidentReportDiv').style.display ="block";
    
                }else if (report_name == "patient_assessment"){
                    // console.log(report_id + "patient");
                    ReturnAssessDetails(report_id);
                    document.getElementById('incidentReportDiv').style.display ="none";
                    document.getElementById('patientAssessmentDiv').style.display = "block";
                }
            })
        });
    
        $(document).on("click", "#sendbtn", function(){
    
            var user_details = {!! json_encode($user_details) !!}
            var msg = $('#msgbox').val();

            SendMessage(msg, user_details);

            //notifications
            // var title = document.getElementById('eventh3').innerHTML;
            // var channel_name = document.getElementById('eventh3').getAttribute('name');
            // var notifmsg = user_details['name'] + ": " + $('#msgbox').val(); 
    
            // $.ajax({
            //    type: "POST",
            //    cache: false,
            //    encoding: "UTF-8",
            //    url: "{{ url('sendnotif') }}",
            //    data:{
            //        type: "msg",
            //        channel_name: channel_name,
            //        sender: user_details['name'],
            //        title: title,
            //        message: notifmsg
            //    },
            //    success: function(data){
            //         SendMessage(msg, user_details);
            //    }
            // });
        });
    
        $(document).on("click", ".eventperson", function(){
            var event_id = jQuery(this).attr("id");
            $("#pagebody").show();
            $.get("{{ url('/conversations') }}/" + event_id, function(data){
                ReturnEventDetails(data);
                ReturnIncidentReports();
                ReturnPatientAssessments();
                ReturnChatPhotos();
            })
        });
    
        $(document).on("click", "#endevent_btn", function(){        
            EndEvent();
            var notify = $.notify({
                // options
                icon: "fa fa-fw fa-bell",
                title: "Event ended",
                message: "Event has ended" 
            },{
                // settings
                type: "success",
                template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<span data-notify="icon"></span> ' +
                    '<span data-notify="title"><b>{1}</b></span><br/> ' +
                    '<span data-notify="message">{2}</span>' +
                '</div>' 
            });
        });
    
        $(document).on('change', '#uploadBtn', function(){
            $imgObj = this.files[0];
            $("#msgbox").val($imgObj.name);
            
            var user_details = {!! json_encode($user_details) !!}
    
            SendImage($imgObj, user_details);

            //notifications
            // var title = document.getElementById('eventh3').innerHTML;
            // var channel_name = document.getElementById('eventh3').getAttribute('name');
            // var notifmsg = user_details['name'] + " sent an image";
    
    
            // var msg = $('#msgbox').val();
    
    
            // $.ajax({
            //    type: "POST",
            //    cache: false,
            //    encoding: "UTF-8",
            //    url: "{{ url('sendnotif') }}",
            //    data:{
            //        type: "msg",
            //        channel_name: channel_name,
            //        sender: user_details['name'],
            //        title: title,
            //        message: notifmsg
            //    },
            //    success: function(data){
            //         SendImage($imgObj, user_details);
            //    }
            // });
        })
    
        $(document).ready(function(){
            ReadOngoingEvents();
            
            $('.eventperson').trigger("click");
    
            $(function() {
                $('.pop').on('click', function() {
                    $('.imagepreview').attr('src', $(this).find('img').attr('src'));
                    $('#imagemodal').modal('show');   
                });
            });
        });
    </script>

    <div class="container col-sm-12">
        <div class="row">
            <div class="col-sm-3">  
                <div>
                    <h4>Ongoing Events</h4>
                    <div  style="overflow-y: auto; height: 650px">
                        <div id="ongoing_div">
                            {{--  list of ongoing events here  --}}
                        </div>
                    </div>
                </div>
            </div>
        
            <div id="pagebody" class="col-sm-9" style="display:none">
                <div class="row">
                    <div class="col-sm-9">
                        <h3 id="eventh3">
                            {{--  event name here  --}}
                        </h3>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" id="endevent_btn" class="btn btn-success">End Event</button>
                    </div>
                </div>
                <div class="row">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li id="convoTab" class="nav-item">
                            <a class="nav-link" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="true">Information</a>
                        </li>
                        <li id="convoTab" class="nav-item">
                            <a class="nav-link active" id="convo-tab" data-toggle="tab" href="#convo" role="tab" aria-controls="convo" aria-selected="true">Conversation</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="reports-tab" data-toggle="tab" href="#reports" role="tab" aria-controls="reports" aria-selected="false">Reports</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="photos-tab" data-toggle="tab" href="#photos" role="tab" aria-controls="photos" aria-selected="false">Photos</a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade" id="info" role="tabpanel" aria-labelledby="info-tab">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <b><label for="eEventName">Event Name</label></b>
                                        <input type="text" class="form-control" id="eEventName" placeholder="Event Name" readonly>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <b><label for="eEventType">Event Type</label></b>
                                        <input type="text" class="form-control" id="eEventType" placeholder="Event Type" readonly>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <b><label for="eLandmark">Nearest Landmark</label></b>
                                        <input type="text" class="form-control" id="eLandmark" placeholder="Landmark" readonly>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <small><label for="eLatitude">Latitude</label></small>
                                        <input id="eLatitude" type="text" class="form-control" placeholder="Latitude" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <small><label for="eLongitude">Longitude</label></small>
                                        <input id="eLongitude" type="text" class="form-control" placeholder="Longitude" readonly>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <b><label for="eTimeOfCall">Time of Call</label></b>
                                        <input id="eTimeOfCall" type="text" class="form-control" placeholder="Time of Call" readonly>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <b><label for="eEndTime">End Time</label></b>
                                        <input id="eEndTime" type="text" class="form-control" placeholder="End Time" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div id="map5" style="height: 100%; margin-top:1%; margin-bottom:1%;"></div>
        
                                <div class="form-row">
                                    <script async defer
                                        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBP5kXEpIl5cRZ7xOc65AIm1NWZt1-WtkQ&libraries=places&callback=initMap">
                                    </script>
                                </div>
                            </div>
                        </div>                    
                    </div>
    
    
                    <div class="tab-pane fade show active" id="convo" role="tabpanel" aria-labelledby="convo-tab">
                        <div id="chatbody" class="row chatbody">
                            <table class="table" id="convo_table">
                                {{--  list of messages here  --}}
                            </table>
                        </div>
                    
                        <div class="row">
                            <div class="form-group col-sm-8">
                                <input id="msgbox" class="form-control" placeholder="Enter message...">
                                <ul id="recordingslist"></ul>
                            </div>
                            <div class="col-sm-2">
                                <button id="sendbtn" class="btn btn-info btn-block">Send</button>
                            </div>
                            <div class="col-sm-1">
                                <div class="inputWrapper" data-provides="fileinput">
                                    <span class="btn btn-default btn-file">
                                        <i class="fa fa-fw fa-image"></i>
                                        <input id="uploadBtn" class="fileInput" type="file" name="file1"/>
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="voiceWrapper" data-provides="fileinput">
                                    {{-- <span class="btn btn-default"> --}}
                                        
                                        <button class="btn btn-info btn-block" onclick="startRecording(this);"><i class="fa fa-fw fa-microphone"></i></button>
                                    {{-- </span> --}}
                                    
                                    <button class="btn btn-info btn-block" onclick="stopRecording(this);" style="display: none"><i class="fa fa-fw fa-microphone-slash"></i></button>
    
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="reports" role="tabpanel" aria-labelledby="reports-tab">
                        <div class="row">
                            <div class="col-md-4">
                                <h4>Reports</h4>
                                <div id="reports_div">
                                    {{--  list of reports here  --}}
                                </div>
                            </div>
                            {{--  <div class="formDiv">  --}}
                                <div class="col-md-8" id="incidentReportDiv" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h3>Incident Report</h3>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" id="incidentExport_btn" class="btn btn-info btn-block">Export to Excel</button> 
                                        </div>
                                    </div> 
                                    <div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <b><label for="iDate">Date</label></b>
                                                <input type="text" class="form-control" id="iDate" placeholder="Date">
                                            </div>
                                            
                                            <div class="form-group col-md-6">
                                                <b><label for="iTimeOfCall">Time of Call</label></b>
                                                <input type="text" class="form-control" id="iTimeOfCall" placeholder="Time of Call">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <b><label for="iCallReceiver">Call Receiver</label></b>
                                                <input type="text" class="form-control" id="iCallReceiver" placeholder="Call Receiver">
                                            </div>
                    
                                            <div class="form-group col-md-6">
                                                <b><label for="iTimeOfArrival">Time of Arrival at the Location</label></b>
                                                <input type="text" class="form-control" id="iTimeOfArrival" placeholder="Time of Arrival at Location">
                                            </div>
                                        </div>
                                        <div class="form-row">                            
                                            <div class="form-group col-md-12">
                                                <b><label for="iLocation">Location</label></b>
                                                <input type="text" class="form-control" id="iLocation" placeholder="1234 Main St">
                                            </div>
                                        </div>
                                    </div>
                    
                                    <h3>Patient Profile</h3>
                                    <div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <b><label for="iName">Name</label></b>
                                                <input type="text" class="form-control" id="iName" placeholder="Patient's Name">
                                            </div>
                                            
                                            <div class="form-group col-md-6">
                                                <b><label for="iAddress">Address</label></b>
                                                <input type="text" class="form-control" id="iAddress" placeholder="Patient's Address">
                                            </div>
                    
                                            
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <b><label for="iGender">Gender</label></b>
                                                <select id="iGender" class="form-control">
                                                    <option>Male</option>
                                                    <option>Female</option>
                                                </select>
                                            </div>
                    
                                            <div class="form-group col-md-4">
                                                <b><label for="iAge">Age</label></b>
                                                <input type="text" class="form-control" id="iAge" placeholder="Patient's Age">
                                            </div>
                    
                                            <div class="form-group col-md-4">                                
                                            </div>
                                        </div>
                                        <div class="form-row">                            
                                            <div class="form-group col-md-12">
                                                <b><label for="iAssessment">Initial Assessment</label></b>
                                                <input type="text" class="form-control" id="iAssessment" placeholder="Initial Assessment">
                                            </div>
                                        </div>
                                    </div>
                                </div> 
        
        
        
        
        
        
        
                                <div class="col-md-8" id="patientAssessmentDiv" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h3>Patient Assessment</h3>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" id="assessExport_btn" class="btn btn-info btn-block">Export to Excel</button> 
                                        </div>
                                    </div>
                                    <div>
                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <b><label for="pDate">Date</label></b>
                                                <input type="text" class="form-control" id="pDate" placeholder="Date">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b><label for="pLocation">Location</label></b>
                                                <input type="text" class="form-control" id="pLocation" placeholder="Location">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <b><label for="pTime">Time</label></b>
                                                <input type="text" class="form-control" id="pTime" placeholder="Time">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <b><label for="pFamilyName">Patient's Family Name</label></b>
                                                <input type="text" class="form-control" id="pFamilyName" placeholder="Patient's Family Name">
                                            </div>
                    
                                            <div class="form-group col-md-4">
                                                <b><label for="pGivenName">Given Name</label></b>
                                                <input type="text" class="form-control" id="pGivenName" placeholder="Patient's Given Name">
                                            </div>
    
                                            <div class="form-group col-md-2">
                                                <b><label for="pSex">Sex</label></b>
                                                <select id="pSex" class="form-control">
                                                    <option>M</option>
                                                    <option>F</option>
                                                </select>
                                            </div>
    
                                            <div class="form-group col-md-2">
                                                <b><label for="pDOB">DOB</label></b>
                                                <input type="text" class="form-control" id="pDOB" placeholder="DOB">
                                            </div>
                                        </div>
                                        <div class="form-row">   
                                            <div class="form-group col-md-8">
                                                <b><label for="pAddress">Patient's Address</label></b>
                                                <input type="text" class="form-control" id="pAddress" placeholder="Patient's Address">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <b><label for="pTelephone">Telephone</label></b>
                                                <input type="text" class="form-control" id="pTelephone" placeholder="Telephone">
                                            </div>
                                        </div>
                                        <div class="form-row">   
                                            <div class="form-group col-md-6">
                                                <b><label for="pAllergies">Allergies?</label></b>
                                                <input type="text" class="form-control" id="pAllergies" placeholder="Allergies?">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b><label for="pMedications">Medications</label></b>
                                                <input type="text" class="form-control" id="pMedications" placeholder="Medications">
                                            </div>
                                        </div>
                                        <div class="form-row">   
                                            <div class="form-group col-md-12">
                                                <b><label for="pWhatHappened">What happened?</label></b>
                                                <input type="text" class="form-control" id="pWhatHappened" placeholder="What happened?">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <b><label for="pWitnessFamilyName">Witness' Family Name</label></b>
                                                <input type="text" class="form-control" id="pWitnessFamilyName" placeholder="Witness' Family Name">
                                            </div>
                    
                                            <div class="form-group col-md-4">
                                                <b><label for="pWitnessGivenName">Given Name</label></b>
                                                <input type="text" class="form-control" id="pWitnessGivenName" placeholder="Witness' Given Name">
                                            </div>
    
                                            <div class="form-group col-md-4">
                                                <b><label for="pWitnessTelephone">Telephone</label></b>
                                                <input type="text" class="form-control" id="pWitnessTelephone" placeholder="Telephone">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <b><p>Past Medical History</p></b>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="cNotKnown" value="Not known">
                                                <label class="form-check-label" for="cNotKnown">Not known</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="cAsthma" value="Asthma">
                                                <label class="form-check-label" for="cAsthma">Asthma</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="cCardiac" value="Cardiac">
                                                <label class="form-check-label" for="cCardiac">Cardiac</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="cDiabetic" value="Diabetic">
                                                <label class="form-check-label" for="cDiabetic">Diabetic</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="cEpilepsy" value="Epilepsy">
                                                <label class="form-check-label" for="cEpilepsy">Epilepsy</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="cHypertension" value="Hypertension">
                                                <label class="form-check-label" for="cHypertension">Hypertension</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="cConsciousness" value="Loss of Consciousness">
                                                <label class="form-check-label" for="cConsciousness">Loss of Consciousness</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="cNil" value="Nil">
                                                <label class="form-check-label" for="cNil">Nil</label>
                                            </div>
    
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="cOther" value="Other?">
                                                <label class="form-check-label" for="cOther">Other?</label>
                                            </div>
                                        </div>
                                        <div class="form-row">   
                                            <div class="form-group col-md-2">
                                                <b><label for="pTime1">Time</label></b>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <b><label for="pBreathing1">Breathing</label></b>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <b><label for="pPulse1">Pulse</label></b>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <b><label for="pConsciousLevel1">Conscious Level</label></b>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <b><label for="pOtherObservation1">Other Observation</label></b>
                                            </div>
                                        </div>
                                        <div class="form-row">   
                                            <div class="form-group col-md-2">
                                                <input type="text" class="form-control" id="pTime1" placeholder="Time">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <input type="text" class="form-control" id="pBreathing1" placeholder="Breathing">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <input type="text" class="form-control" id="pPulse1" placeholder="Pulse">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <input type="text" class="form-control" id="pConsciousLevel1" placeholder="Conscious Level">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <input type="text" class="form-control" id="pOtherObservation1" placeholder="Other Observation">
                                            </div>
                                        </div>
                                        <div class="form-row">   
                                            <div class="form-group col-md-2">
                                                <input type="text" class="form-control" id="pTime2" placeholder="Time">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <input type="text" class="form-control" id="pBreathing2" placeholder="Breathing">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <input type="text" class="form-control" id="pPulse2" placeholder="Pulse">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <input type="text" class="form-control" id="pConsciousLevel2" placeholder="Conscious Level">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <input type="text" class="form-control" id="pOtherObservation2" placeholder="Other Observation">
                                            </div>
                                        </div>
                                        <div class="form-row">   
                                            <div class="form-group col-md-2">
                                                <input type="text" class="form-control" id="pTime3" placeholder="Time">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <input type="text" class="form-control" id="pBreathing3" placeholder="Breathing">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <input type="text" class="form-control" id="pPulse3" placeholder="Pulse">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <input type="text" class="form-control" id="pConsciousLevel3" placeholder="Conscious Level">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <input type="text" class="form-control" id="pOtherObservation3" placeholder="Other Observation">
                                            </div>
                                        </div>
                                        <div class="form-row">   
                                            <div class="form-group col-md-12">
                                                <b><label for="pRefuseTreatment">Refuse Treatment</b> <small>witness name, number and signature</small></label>
                                                <input type="text" class="form-control" id="pRefuseTreatment" placeholder="Refuse Treatment">
                                            </div>
                                        </div>
                                        <div class="form-row">   
                                            <div class="form-group col-md-12">
                                                <b>Discharge</b> <small>How?</small>
                                            </div>
                                            <div class="form-group col-md-12">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="rDischarge" id="rAmbulance" value="Ambulance">
                                                        <label class="form-check-label" for="rAmbulance">Ambulance</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="rDischarge" id="rHospital" value="Hospital">
                                                        <label class="form-check-label" for="rHospital">Hospital</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="rDischarge" id="rOwnDoctor" value="Own Doctor">
                                                        <label class="form-check-label" for="rOwnDoctor">Own Doctor</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="rDischarge" id="rReturnToWork" value="Return to Work">
                                                        <label class="form-check-label" for="rReturnToWork">Return to Work</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="rDischarge" id="rOther" value="Other">
                                                        <label class="form-check-label" for="rOther">Other</label>
                                                    </div>
                                            </div>
                                        </div>
                                        <div class="form-row">   
                                            <div class="form-group col-md-6">
                                                <b><label for="pFirstAider">First Aider Name and signature</label></b>
                                                <input type="text" class="form-control" id="pFirstAider" placeholder="First aider name and signature">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <b><label for="pDoctorsSignature">Doctor's Signature</label></b>
                                                <input type="text" class="form-control" id="pDoctorsSignature" placeholder="Doctor's Signature">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <b><label for="pTimeOut">Time Out</label></b>
                                                <input type="text" class="form-control" id="pTimeOut" placeholder="Time Out">
                                            </div>
                                        </div>
                                    </div>
                                </div>
    
    
                            {{--  </div>  --}}
                            
                        </div>
                        
                    </div>
                    <div class="tab-pane fade" id="photos" role="tabpanel" aria-labelledby="photos-tab">
                        <div id="photos_div">
                            {{--  photo thumbnails are displayed here  --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        
<script>
    var audio_context;
    var recorder;
    function startUserMedia(stream) {
        var input = audio_context.createMediaStreamSource(stream);
        console.log('Media stream created.');
        // Uncomment if you want the audio to feedback directly
        //input.connect(audio_context.destination);
        //console.log('Input connected to audio context destination.');
        
        recorder = new Recorder(input);
        console.log('Recorder initialised.');
    }
    function startRecording(button) {
        recorder && recorder.record();
        button.style.display = "none";
        button.nextElementSibling.style.display = "block";
        console.log('Recording...');
    }
    function stopRecording(button) {
        recorder && recorder.stop();
        button.style.display = "none";
        button.previousElementSibling.style.display = "block";
        console.log('Stopped recording.');
        
        // create WAV download link using audio data blob
        // createDownloadLink();
        var user_details = {!! json_encode($user_details) !!}
        SendAudio(user_details);
        
        recorder.clear();
    }
    function createDownloadLink() {
        
    }
    window.onload = function init() {
        try {
        // webkit shim
        window.AudioContext = window.AudioContext || window.webkitAudioContext;
        navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia;
        window.URL = window.URL || window.webkitURL;
        
        audio_context = new AudioContext;
        console.log('Audio context set up.');
        console.log('navigator.getUserMedia ' + (navigator.getUserMedia ? 'available.' : 'not present!'));
        } catch (e) {
        alert('No web audio support in this browser!');
        }
        
        navigator.getUserMedia({audio: true}, startUserMedia, function(e) {
        console.log('No live audio input: ' + e);
        });
    };
</script>
<script src="{{ asset('js/voice.js') }}"></script>

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