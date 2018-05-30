var db = firebase.firestore();

var event_summary = {
    type: null,
    lat: null,
    long: null,
    landmark: null,
    responders: null,
    responders_id: null
};

function AssignType(info){
    event_summary.type = info;
}    

$(document).ready(function() {

    ReturnResponders();
    
    var navListItems = $('ul.setup-panel li a'),
        allWells = $('.setup-content');

    allWells.hide();

    navListItems.click(function(e)
    {
        e.preventDefault();
        var $target = $($(this).attr('href')),
            $item = $(this).closest('li');
            

        if (!$item.hasClass('disabled')) {
            navListItems.closest('  ').removeClass('active');
            $item.addClass('active');
            allWells.hide();                
            $target.show();
        }
    });
    
    $('ul.setup-panel li.active a').trigger('click');
    
    $('#activate-step-2').on('click', function(e) {
        $('ul.setup-panel li:eq(1)').removeClass('disabled');
        $('ul.setup-panel li a[href="#step-2"]').trigger('click');

        if(event_summary.type == null){
            $("#event_alert").removeClass("in").show();
            $("#event_alert").delay(200).addClass("in").fadeOut(3000);
        }
    });

    $('#activate-step-3').on('click', function(e) {
        $('ul.setup-panel li:eq(2)').removeClass('disabled');
        $('ul.setup-panel li a[href="#step-3"]').trigger('click');

        event_summary.lat = document.getElementById("lat2").value;
        event_summary.long = document.getElementById("long2").value;
        event_summary.landmark = document.getElementById("location-text-box").value;
        eventLocation.lat = document.getElementById("lat2").value;
        eventLocation.lng = document.getElementById("long2").value;

        for (var i = 0; i < respondersLoc.length; i++) {
             distanceMatrix(respondersLoc[i]);
        }

        if(document.getElementById("lat2").value != '' && document.getElementById("long2").value != ''){
            UpdateCenter("map3", parseFloat(event_summary.lat), parseFloat(event_summary.long));
            PrintResponders();
        }else{
            $("#location_alert").removeClass("in").show();
            $("#location_alert").delay(200).addClass("in").fadeOut(3000);
        }


    });

    $('#activate-step-4').on('click', function(e) {
        $('ul.setup-panel li:eq(3)').removeClass('disabled');
        $('ul.setup-panel li a[href="#step-4"]').trigger('click');

        var checkboxes = document.getElementsByName('chkresponder');
        var checkboxesChecked = [];
        var resIds = [];
        // loop over them all
        for (var i=0; i<checkboxes.length; i++) {
            // And stick the checked ones onto an array...
            if (checkboxes[i].checked) {
                var value = document.getElementById('cCheck' + (i)).innerHTML;                    
                var resId = document.getElementById('cCheck' + (i)).getAttribute("name");

                resIds.push(resId);
                checkboxesChecked.push(value);
            }
        }
        
        event_summary.responders = checkboxesChecked;
        event_summary.responders_id = resIds;
        
        var notifyEveryone = document.getElementById('notifyEveryone').checked;
                

        if(event_summary.responders.length > 0 || notifyEveryone == true){
            document.getElementById('inputEvent').value = event_summary.type;
            document.getElementById('inputLocation').value = event_summary.landmark;
            document.getElementById('inputLatitude').value = event_summary.lat;
            document.getElementById('inputLongitude').value = event_summary.long;
            
            UpdateCenter("map4", parseFloat(event_summary.lat), parseFloat(event_summary.long));
            PrintChosenResponders(resIds);
        }else{
            $("#responders_alert").removeClass("in").show();
            $("#responders_alert").delay(200).addClass("in").fadeOut(3000);
        }
    });

    document.getElementById('submitbtn').onclick = function(){
        var today = new Date();
        var event_id = today.getFullYear() + ('0' + (today.getMonth() + 1)).slice(-2) + ('0' + today.getDate()).slice(-2) + "_" + ('0' + today.getHours()).slice(-2) + "" + ('0' + today.getMinutes()).slice(-2) + "_" + event_summary.type;            
        AddEvent(event_summary, event_id);
        
        
        var title = "Event added"
        var msg = event_summary.type + " has been reported at " + event_summary.landmark;            

        $("#successevent_alert").removeClass("in").show();

        event_summary = {
            type: null,
            lat: null,
            long: null,
            landmark: null,
            responders: null,
            responders_id: null
        }; 

        //notifications
        // $.ajax({
        //     type: "POST",
        //     cache: false,
        //     encoding: "UTF-8",
        //     url: "{{ url('sendnotif') }}",
        //     data:{
        //         type: "event",
        //         channel_name: "cmd_center",
        //         sender: event_id,
        //         title: title,
        //         message: msg
        //     },
        //     success: function(data){
                
        //     }
        // });
    }

});

var responders = [];
var responders_id = [];

var ReturnResponders = function(){
    db.collection("Responders").orderBy("userType").onSnapshot(function(querySnapshot){    
        
        querySnapshot.forEach(function(doc){
            if(!responders.includes(doc.data())){
                responders.push(doc.data());
                responders_id.push(doc.id);
            }
        });
    });
}

function PrintResponders(){
    var d1 = document.getElementById('responderslist_div');

    for(var i=0; i < responders.length; i++){
        if(document.getElementById('cCheck'+i) == null){

            
            var usertype = "primary";
            var badgetext = "";
            if(responders[i].userType == "Fire Responder"){
                usertype = "warning";
                badgetext = "FR";
            }          
            else if (responders[i].userType == "Medical Responder"){
                usertype = "info";
                badgetext = "MR";
            }
            else if (responders[i].userType == "Command Center Officer"){
                usertype = "secondary";
                badgetext = "CC";
            }
            var resName = responders[i].username.replace(/ /g,"_");

            function findResponder(responders) {
                return responders.username == resName;
              }
            var idx = respondersDistance.findIndex(findResponder);
            
            d1.insertAdjacentHTML('beforeend', '<a href="#" name="'+responders_id[i]+'" id="cCheck'+i+'"class="list-group-item list-group-item-action"><input type="checkbox" class="custom-control-input" id="customCheck'+i+'" name="chkresponder"><label class="custom-control-label" for="customCheck'+i+'" id="cCheck'+i+'"> <span class="badge badge-'+usertype+'">'+badgetext+'</span>   '+responders[i].username+'</label><div id="' + resName + '_distance">'+ respondersDistance[idx].text +'</div><span class="badge progress-bar-success float-right"> </span></a>');
        }
    }
}

function PrintChosenResponders(chosenresponders_id){
    var d2 = document.getElementById('chosenResponders_div');
    $("#chosenResponders_div").html("");

    for(var i=0; i < chosenresponders_id.length; i++){
        for(var j = 0; j < responders_id.length; j++){
            if(chosenresponders_id[i] == responders_id[j]){
                d2.insertAdjacentHTML('beforeend', '<a href="#" name="'+responders_id[j]+'" id="cCheck'+j+'"class="list-group-item list-group-item-action"><input type="checkbox" class="custom-control-input" id="customCheck'+j+'" name="chkresponder"><label class="custom-control-label" for="customCheck'+j+'" id="cCheck'+j+'">'+responders[j].username+'</label><div id="' +responders[j].username + '_distance"><small>69 meters away</small></div><span class="badge progress-bar-success float-right"> </span></a>');
                break;
            }
        }
    }
}

function AddEvent(event_summary, event_id){

    console.log(event_summary);    

    var today = new Date();
    // var event_id = today.getFullYear() + ('0' + (today.getMonth() + 1)).slice(-2) + ('0' + today.getDate()).slice(-2) + "_" + ('0' + today.getHours()).slice(-2) + "" + ('0' + today.getMinutes()).slice(-2) + "_" + event_summary.type;    

    var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var timestamp = monthNames[today.getMonth()] + " " + today.getDate() + ", " + today.getFullYear() + " - " + ('0' + today.getHours()).slice(-2) + ":" + ('0' + today.getMinutes()).slice(-2);
    
    var event_type;
    switch(event_summary.type){
        case "fire":
            event_type = "Fire Incident";
            break;
        case "vehicle_accident":
        event_type = "Vehicular Accident";
            break;
        case "medical":
        event_type = "Medical Emergency";
            break;
    }


    var eventsRef = db.collection('Events');
    eventsRef.doc(event_id).set({
        eventId:event_id,
        eventName: timestamp,
        eventType: event_type,
        location: event_summary.landmark,
        mapLocation: new firebase.firestore.GeoPoint(parseFloat(event_summary.lat), parseFloat(event_summary.long)),
        status: "Ongoing",
        timeOfCall: firebase.firestore.FieldValue.serverTimestamp()
    }).then(function(){        
        console.log("Saved!");
    }).catch(function(error){
        console.error("Error: " + error);
    });


    for(var i = 0; i < event_summary.responders_id.length; i++){        
        eventsRef.doc(event_id).collection("Responders").add({
            id: db.collection("Responders").doc(event_summary.responders_id[i])
        }).then(function(){
            console.log("Saved responders");
        }).catch(function(error){
            console.error("Error: " + error);
        });
    }
}