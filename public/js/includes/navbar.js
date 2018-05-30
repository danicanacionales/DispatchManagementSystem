var db = firebase.firestore();

var responders = [];
var responders_id = [];

var ReturnResponders = function(){
    db.collection("Responders").onSnapshot(function(querySnapshot){    
        
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
            d1.insertAdjacentHTML('beforeend', '<a href="#" name="'+responders_id[i]+'" id="cCheck'+i+'"class="list-group-item list-group-item-action"><input type="checkbox" class="custom-control-input" id="customCheck'+i+'" name="chkresponder"><label class="custom-control-label" for="customCheck'+i+'" id="cCheck'+i+'">'+responders[i].username+'</label><small>100 meters away</small><span class="badge progress-bar-success float-right"> </span></a>');
        }
    }
}

// function PrintChosenResponders(chosenresponders_id){
//     var d2 = document.getElementById('chosenResponders_div');

//     for(var i=0; i < chosenresponders_id.length; i++){
//         for(var j = 0; j < responders_id.length; j++){
//             if(chosenresponders_id[i] == responders_id[j]){
//                 d2.insertAdjacentHTML('beforeend', '<a href="#" name="'+responders_id[j]+'" id="cCheck'+j+'"class="list-group-item list-group-item-action"><input type="checkbox" class="custom-control-input" id="customCheck'+j+'" name="chkresponder"><label class="custom-control-label" for="customCheck'+j+'" id="cCheck'+j+'">'+responders[j].username+'</label><small>100 meters away</small><span class="badge progress-bar-success float-right"> </span></a>');
//                 break;
//             }
//         }
//     }
// }

// function AddEvent(event_summary){

//     console.log(event_summary);    

//     var today = new Date();
//     var event_id = today.getFullYear() + ('0' + (today.getMonth() + 1)).slice(-2) + ('0' + today.getDate()).slice(-2) + "_" + ('0' + today.getHours()).slice(-2) + "" + ('0' + today.getMinutes()).slice(-2) + "_" + event_summary.type;    

//     var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
//     var timestamp = monthNames[today.getMonth()] + " " + today.getDate() + ", " + today.getFullYear() + " - " + ('0' + today.getHours()).slice(-2) + ":" + ('0' + today.getMinutes()).slice(-2);
    
//     var event_type;
//     switch(event_summary.type){
//         case "fire":
//             event_type = "Fire Incident";
//             break;
//         case "vehicle_accident":
//         event_type = "Vehicular Accident";
//             break;
//         case "medical":
//         event_type = "Medical Emergency";
//             break;
//     }


//     var eventsRef = db.collection('Events');
//     eventsRef.doc(event_id).set({
//         eventId:event_id,
//         eventName: timestamp,
//         eventType: event_type,
//         location: event_summary.landmark,
//         mapLocation: new firebase.firestore.GeoPoint(parseFloat(event_summary.lat), parseFloat(event_summary.long)),
//         status: "Ongoing",
//         timeOfCall: firebase.firestore.FieldValue.serverTimestamp()
//     }).then(function(){
//         console.log("Saved!");
//     }).catch(function(error){
//         console.error("Error: " + error);
//     });


//     for(var i = 0; i < event_summary.responders_id.length; i++){        
//         eventsRef.doc(event_id).collection("Responders").add({
//             id: db.collection("Responders").doc(event_summary.responders_id[i])
//         }).then(function(){
//             console.log("Saved responders");
//             $("#modal4").modal('hide');
//             $("#modal4-backdrop").remove();
//         }).catch(function(error){
//             console.error("Error: " + error);
//         });
//     }
// }
