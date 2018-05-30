var db = firebase.firestore();
var storage = firebase.storage();

var selectedEvent_id;

function ReturnEventImage(event){
    var imgUrl;
    switch(event.eventType){
        case "Vehicular Accident":
            imgUrl = "https://firebasestorage.googleapis.com/v0/b/c3chat-782d3.appspot.com/o/Default%2Fvehicle_accident.png?alt=media&token=2f611a3f-c479-4435-81dc-692d38fd8cca";
            break;
        case "Medical Emergency":
            imgUrl = "https://firebasestorage.googleapis.com/v0/b/c3chat-782d3.appspot.com/o/Default%2Fmedical.png?alt=media&token=58cc9756-4fac-4dd7-af79-d2cce5ec02bc";
            break;
        case "Fire Incident":
            imgUrl = "https://firebasestorage.googleapis.com/v0/b/c3chat-782d3.appspot.com/o/Default%2Fburn.png?alt=media&token=76bb8de2-c281-49c1-8915-917b5bb70eab";
            break;
            default:
            imgUrl = "http://via.placeholder.com/50x50?text=E";
    }

    return imgUrl;
}

var ReadOngoingEvents = function(){
    var d1 = document.getElementById('ongoing_div');
    db.collection("Events").where("status", "==", "Ongoing").onSnapshot(function(querySnapshot){    

        querySnapshot.docChanges.forEach(function(change){
            if(change.type === "added"){
                
                var imgUrl = ReturnEventImage(change.doc.data());
                d1.insertAdjacentHTML('beforeend', '<a href="#" id="'+change.doc.id+'" class="eventperson"><span class="eventimg"><img src="'+imgUrl+'" alt="" /></span><div class="nameevent"><div class="pname">'+change.doc.data().eventName+'</div><div class="lastmsg">'+change.doc.data().location+'</div></div></a>');
            }
            if(change.type === "removed"){
                document.getElementById(change.doc.id).remove();
            }
        })
    });
}

function ReturnEventDetails(event_id){
    if(event_id != selectedEvent_id){
        $("#convo_table").html("");
        $("#photos_div").html("");
        $("#reports_div").html("");
        selectedEvent_id = event_id;

        var convoRef = db.collection("Events").doc(event_id).collection("Conversation");
        var respRef = db.collection("Responders");
        
        db.collection("Events").doc(event_id).get().then(snap =>{        
            $("#eventh3").html(snap.data().eventName);
            $("#eventh3").attr('name', snap.id);
            document.getElementById("eEventName").value = snap.data().eventName;
            document.getElementById("eEventType").value = snap.data().eventType;
            document.getElementById("eLandmark").value = snap.data().location;
            document.getElementById("eLatitude").value = snap.data().mapLocation.latitude;
            document.getElementById("eLongitude").value = snap.data().mapLocation.longitude;
            document.getElementById("eTimeOfCall").value = snap.data().timeOfCall;
            document.getElementById("eEndTime").value = "";
            UpdateCenter("map5", snap.data().mapLocation.latitude, snap.data().mapLocation.longitude); 
        });
        
        convoRef.orderBy("timestamp").onSnapshot(function(querySnapshot){
            var d1 = document.getElementById('convo_table');
            querySnapshot.docChanges.forEach(function(change){ //for each message
                if(change.type === "added"){                    
                    var date = new Date(change.doc.data().timestamp);
                    var msgTimestamp = new Date(change.doc.data().timestamp);
                    console.log(change.doc.data());
                    var diff;
                    if(change.doc.data().timestamp != null){
                        console.log(msgTimestamp);

                        difference = GetTimeDifference(msgTimestamp);
                        if(difference != "-1 seconds ago")
                            diff = difference
                        else
                            diff = "now";
                    }
                    else{
                        diff = "now";
                    }
                        

                    var avatarUrl;

                    respRef.get().then(function(snap){
                        snap.forEach(function(doc){
                            if(doc.data().username == change.doc.data().sender){
                                // console.log(doc.data().username + " == " + change.doc.data().sender)
                                avatarUrl = doc.data().avatarUrl;

                               
                                if(change.doc.data().type == "text")
                                    d1.insertAdjacentHTML('beforeend', '<tr><td><img class="d-flex mr-3 rounded-circle" src="'+avatarUrl+'" style="max-height: 50px" /></td><td>'+change.doc.data().message+'</td><td>'+diff+'</td></tr>');
                                else if(change.doc.data().type == "image")
                                    d1.insertAdjacentHTML('beforeend', '<tr><td><img class="d-flex mr-3 rounded-circle" src="'+avatarUrl+'" style="max-height: 50px" /></td><td><img src="'+change.doc.data().url+'" style="max-height:300px" /></td><td>'+diff+'</td></tr>');
                                else if (change.doc.data().type == "voice")
                                    d1.insertAdjacentHTML('beforeend', '<tr><td><img class="d-flex mr-3 rounded-circle" src="'+avatarUrl+'" style="max-height: 50px" /></td><td><audio controls><source src="'+change.doc.data().url+'" type="audio/aac"></audio></td><td>'+diff+'</td></tr>');
                                else if (change.doc.data().type == "audio")
                                    d1.insertAdjacentHTML('beforeend', '<tr><td><img class="d-flex mr-3 rounded-circle" src="'+avatarUrl+'" style="max-height: 50px" /></td><td><audio controls><source src="'+change.doc.data().url+'" type="audio/wav"></audio></td><td>'+diff+'</td></tr>');
                            }
                            // $("#chatbody").animate({ scrollTop: $(document).height() });
                        })
                    })
                }
            });
        });
    }
}

function GetTimeDifference(previous){
    console.log(previous);
    var current = Date.now();

    var msPerMinute = 60 * 1000;
    var msPerHour = msPerMinute * 60;
    var msPerDay = msPerHour * 24;
    var msPerMonth = msPerDay * 30;
    var msPerYear = msPerDay * 365;

    var elapsed = current - previous;

    if (elapsed < msPerMinute) {
         return Math.round(elapsed/1000) + ' seconds ago';   
    }

    else if (elapsed < msPerHour) {
         return Math.round(elapsed/msPerMinute) + ' minutes ago';   
    }

    else if (elapsed < msPerDay ) {
         return Math.round(elapsed/msPerHour ) + ' hours ago';   
    }

    else if (elapsed < msPerMonth) {
        return Math.round(elapsed/msPerDay) + ' days ago';   
    }

    else if (elapsed < msPerYear) {
        return Math.round(elapsed/msPerMonth) + ' months ago';   
    }

    else {
        return Math.round(elapsed/msPerYear ) + ' years ago';   
    }
}

function ReturnChatPhotos(){
    var convoRef = db.collection("Events").doc(selectedEvent_id).collection("Conversation");

    convoRef.orderBy("timestamp").onSnapshot(function(querySnapshot){
        var d1 = document.getElementById('photos_div');
        querySnapshot.docChanges.forEach(function(change){ //for each message
            if(change.type === "added"){
                if(change.doc.data().type == "image")                    
                    d1.insertAdjacentHTML('beforeend', '<span class="docuimg"><a href="#" class="pop"><img class="img-thumbnail" src="'+change.doc.data().url+'" alt="" /></a></span>');
            }
        });
    });
}

function SendMessage(message,user_details){
    var convoRef = db.collection("Events").doc(selectedEvent_id);

    convoRef.collection("Conversation").add({
        message: message,
        type: "text",
        timestamp: firebase.firestore.FieldValue.serverTimestamp(),
        sender: user_details["name"], // change this to the user's logged in
        imgUrl: null,
        uid: user_details["name"]
    }).then(function(){
        document.getElementById('msgbox').value = '';    
        console.log("Message Sent!");
    }).catch(function(error){
        console.error("Error: " + error);
    })
}

function SendImage(file, user_details){    
    console.log("uploading...")
    
    var storageRef = firebase.storage().ref().child("/Photos");
    
    var today = new Date();
    var newFileName = "C3" + today.getFullYear() + ('0' + (today.getMonth() + 1)).slice(-2) + ('0' + today.getDate()).slice(-2) + "_" + ('0' + today.getHours()).slice(-2) + "" + ('0' + today.getMinutes()).slice(-2) + ('0' + today.getSeconds()).slice(-2);

    var uploadTask = storageRef.child(newFileName).put(file);
    
    uploadTask.on(firebase.storage.TaskEvent.STATE_CHANGED, function(snapshot){
        var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;

        document.getElementById('sendbtn').innerHTML = progress + "% uploaded";
    }, function(error){

    },function(){
        var convoRef = db.collection("Events").doc(selectedEvent_id);
        convoRef.collection("Conversation").add({
            message: null,
            type: "image",
            timestamp: firebase.firestore.FieldValue.serverTimestamp(),
            sender: user_details["name"], // change this to the user's logged in
            url: uploadTask.snapshot.downloadURL,
            uid: user_details["name"]
        });

        document.getElementById('sendbtn').innerHTML = "Send";
        document.getElementById('msgbox').value = '';
    });
}

function SendAudio(user_details){
    recorder && recorder.exportWAV(function(blob) {

        var storageRef = firebase.storage().ref().child("/Audio");
        var today = new Date();
        var newFileName = "C3" + today.getFullYear() + ('0' + (today.getMonth() + 1)).slice(-2) + ('0' + today.getDate()).slice(-2) + "_" + ('0' + today.getHours()).slice(-2) + "" + ('0' + today.getMinutes()).slice(-2) + ('0' + today.getSeconds()).slice(-2);

        var uploadTask = storageRef.child(newFileName).put(blob);

        uploadTask.on(firebase.storage.TaskEvent.STATE_CHANGED, function(snapshot){
            var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
    
            document.getElementById('sendbtn').innerHTML = progress + "% uploaded";
        }, function(error){
    
        },function(){
            var convoRef = db.collection("Events").doc(selectedEvent_id);
            convoRef.collection("Conversation").add({
                message: null,
                type: "audio",
                timestamp: firebase.firestore.FieldValue.serverTimestamp(),
                sender: user_details["name"], // change this to the user's logged in
                url: uploadTask.snapshot.downloadURL,
                uid: user_details["name"]
            });
    
            document.getElementById('sendbtn').innerHTML = "Send";
            document.getElementById('msgbox').value = '';
        });
    });
}


var reports = [];
var reports_id = [];
var patientAssess = [];
var patientAssess_id = [];

function ReturnIncidentReports(){
    var d1 = document.getElementById('reports_div');
    $("#reports_div").html("");
    var incidentRef = db.collection("Events").doc(selectedEvent_id).collection("Incident Reports");

    incidentRef.onSnapshot(function(querySnapshot){
        querySnapshot.docChanges.forEach(function(change){
            if(change.type === "added"){
                reports.push(change.doc.data());
                reports_id.push(change.doc.id);
                d1.insertAdjacentHTML('beforeend', '<a href="#" name="incident_reports" id="'+change.doc.id+'" class="reportperson"><span class="reportimg"><img src="http://via.placeholder.com/50x50?text=A" alt="" /></span><div class="namereport"><div class="pname">Incident Report</div><div class="lastmsg">'+change.doc.data().patientName+' </br> fr. '+change.doc.data().patientAddress+'</div></div></a>');
            }
            if(change.type === "removed"){
                document.getElementById(change.doc.id).remove();
            }
        })
    });
}

function ReturnPatientAssessments(){
    var d1 = document.getElementById('reports_div');

    var patientRef = db.collection("Events").doc(selectedEvent_id).collection("Patient Assessments");

    patientRef.onSnapshot(function(querySnapshot){
        querySnapshot.docChanges.forEach(function(change){
            if(change.type === "added"){
                patientAssess.push(change.doc.data());
                patientAssess_id.push(change.doc.id);
                d1.insertAdjacentHTML('beforeend', '<a href="#" name="patient_assessment" id="'+change.doc.id+'" class="reportperson"><span class="reportimg"><img src="http://via.placeholder.com/50x50?text=A" alt="" /></span><div class="namereport"><div class="pname">Patient Assessment</div><div class="lastmsg">'+change.doc.data().patientGivenName+' '+change.doc.data().patientFamilyName+' </br> fr. '+change.doc.data().patientAddress+'</div></div></a>');
            }
            if(change.type === "removed"){
                document.getElementById(change.doc.id).remove();
            }
        })

        console.log(patientAssess);
    });
}

function ReturnReportDetails(report_id){
   
}

function ReturnAssessDetails(assess_id){
    
}

function EndEvent(){
    var incidentRef = db.collection("Events").doc(selectedEvent_id).update({
        status: "Finished",
        endTime: firebase.firestore.FieldValue.serverTimestamp()
    }).then(function(){
        console.log('event finished');
    });
}

