var map, map2, map3;
var marker, marker2;
var db = firebase.firestore();
var eventLocation;
var respondersLoc = [] ;
var respondersDistance = [];

function initMap() {
  
  //Map options
  var myOptions = {
    center: {lat:14.315320, lng:121.079586},
    mapTypeId: google.maps.MapTypeId.HYBRID,
    zoom: 17,
    styles: [{
      featureType: 'poi',
      stylers: [{ visibility: 'on' }]
    }, {
      featureType: 'transit.station',
      stylers: [{ visibility: 'off' }]
    }],
    disableDoubleClickZoom: true
  }



  //Initialize dispatchers location map (MAP)
  try{
    map = new google.maps.Map(document.getElementById('map1'), myOptions);
    var input = document.getElementById('location-text-box1');
    var autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo('bounds', map);

    var infowindow = new google.maps.InfoWindow();
    marker = new google.maps.Marker({
      map: map,
      anchorPoint: new google.maps.Point(0, -29),
      draggable: true
    });

    google.maps.event.addListener(autocomplete, 'place_changed', function() {
      infowindow.close();
      marker.setVisible(false);
      var place = autocomplete.getPlace();
      if (!place.geometry) {
        return;
      }

      // If the place has a geometry, then present it on a map.
      if (place.geometry.viewport) {
        map.fitBounds(place.geometry.viewport);
      } else {
        map.setCenter(place.geometry.location);
        map.setZoom(17); // Why 17? Because it looks good.
      }
      marker.setIcon( /** @type {google.maps.Icon} */ ({
        url: place.icon,
        size: new google.maps.Size(71, 71),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(17, 34),
        scaledSize: new google.maps.Size(35, 35)
      }));
      marker.setPosition(place.geometry.location);
      marker.setVisible(true);

      var address = '';
      if (place.address_components) {
      address = [
        (place.address_components[0] && place.address_components[0].short_name || ''), (place.address_components[1] && place.address_components[1].short_name || ''), (place.address_components[2] && place.address_components[2].short_name || '')
      ].join(' ');
      }
      document.getElementById("lat").value = place.geometry.location.lat();
      document.getElementById("long").value = place.geometry.location.lng();
    });

    //add locations of responders
    var responderMarker = [];
    db.collection("Responders").get().then(function(querySnapshot) {

      querySnapshot.forEach(function(doc) {
        var responder_loc = {
          'username': doc.data().username, 
          'lat': doc.data().location.latitude,
          'lng': doc.data().location.longitude,
          'avatarUrl': {
            url: doc.data().avatarUrl,
            size: new google.maps.Size(71, 71),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(50, 50)
          }
        };

        // var marker = new CustomMarker(new google.maps.LatLng(responder_loc.lat,responder_loc.lng), map, "https://firebasestorage.googleapis.com/v0/b/c3chat-782d3.appspot.com/o/Photos%2Fusericon.png?alt=media&token=e390282f-0c81-41f1-a209-bdaa4ccef983", responder_loc.username);


        marker = new google.maps.Marker({
          position: {lat: responder_loc.lat, lng: responder_loc.lng},
          map: map,
          title: responder_loc.username,
          icon: responder_loc.avatarUrl
        });
        responderMarker.push(marker);
      });
    });

    db.collection("Responders").orderBy("userType", "desc").onSnapshot(function(querySnapshot) {
      querySnapshot.docChanges.forEach(function(change) {
        var d1 = document.getElementById('responders_div');
        if(change.type === "added" && document.getElementById(change.doc.id) == null){

          var usertype = "primary";
          var badgetext = "";
          if(change.doc.data().userType == "Fire Responder"){
            usertype = "warning";
            badgetext = "FR";
          }          
          else if (change.doc.data().userType == "Medical Responder"){
            usertype = "info";
            badgetext = "MR";
          }
          else if (change.doc.data().userType == "Command Center Officer"){
            usertype = "secondary";
            badgetext = "CC";

          }

          d1.insertAdjacentHTML('beforeend', '<button id="'+change.doc.id+'" name="responder_btn" type="button" class="list-group-item list-group-item-action"><span class="badge badge-'+usertype+'">'+badgetext+'</span>  <b>'+change.doc.data().username+'</b><br/><small>'+change.doc.data().location.latitude+', '+change.doc.data().location.longitude+'</small></button>');
        }

        if(change.type === "modified"){
          lat = change.doc.data().location.latitude;
          lng = change.doc.data().location.longitude;

          function findResponder(responders) {
            return responders.getTitle() == change.doc.data().username;
          }

          var usertype = "primary";
          var badgetext = "";
          if(change.doc.data().userType == "Fire Responder"){
            usertype = "warning";
            badgetext = "FR";
          }          
          else if (change.doc.data().userType == "Medical Responder"){
            usertype = "info";
            badgetext = "MR";
          }
          else if (change.doc.data().userType == "Command Center Officer"){
            usertype = "secondary";
            badgetext = "CC";

          }

          document.getElementById(change.doc.id).innerHTML = "<span class='badge badge-"+usertype+"'>"+badgetext+"</span>   " + "<b>" + change.doc.data().username + "</b><br/><small>" + change.doc.data().location.latitude+ ", " + change.doc.data().location.longitude + "</small>";

          var idx = responderMarker.findIndex(findResponder);          
          responderMarker[idx].setPosition(new google.maps.LatLng( lat, lng ));
        }
      });

      $("button[name='responder_btn']").click(function(event) {
        db.collection("Responders").doc(event.target.id).get().then(function(snap) {          
          map.setCenter({lat: snap.data().location.latitude, lng: snap.data().location.longitude});
        });  
      });
    });


  }catch(error){
    
  }// end dispatchers location map 


  //=====================================================================================================


  //Initialize add event map (MAP2)
  try{

    map2 = new google.maps.Map(document.getElementById('map2'), myOptions);
    var input = document.getElementById('location-text-box');
      var autocomplete = new google.maps.places.Autocomplete(input);
      autocomplete.bindTo('bounds', map2);

      var infowindow = new google.maps.InfoWindow();
      marker2 = new google.maps.Marker({
      map: map2,
        anchorPoint: new google.maps.Point(0, -29),
        draggable: true
      });

    google.maps.event.addListener(autocomplete, 'place_changed', function() {
      infowindow.close();
      marker2.setVisible(false);
      var place = autocomplete.getPlace();
      if (!place.geometry) {
        return;
      }

      // If the place has a geometry, then present it on a map.
      if (place.geometry.viewport) {
        map2.fitBounds(place.geometry.viewport);
      } else {
        map2.setCenter(place.geometry.location);
        map2.setZoom(17); // Why 17? Because it looks good.
      }
      marker2.setIcon( /** @type {google.maps.Icon} */ ({
        url: place.icon,
        size: new google.maps.Size(71, 71),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(17, 34),
        scaledSize: new google.maps.Size(35, 35)
      }));
      marker2.setPosition(place.geometry.location);
      marker2.setVisible(true);

      var address = '';
      if (place.address_components) {
        address = [
          (place.address_components[0] && place.address_components[0].short_name || ''), (place.address_components[1] && place.address_components[1].short_name || ''), (place.address_components[2] && place.address_components[2].short_name || '')
        ].join(' ');
      }
      document.getElementById("lat2").value = place.geometry.location.lat();
      document.getElementById("long2").value = place.geometry.location.lng();
    });

    var service = new google.maps.places.PlacesService(map2);
    google.maps.event.addListener(map2, 'click', function(event) {
      document.getElementById("lat2").value = event.latLng.lat();
      document.getElementById("long2").value = event.latLng.lng();    
      eventLocation = {lat: event.latLng.lat(), lng: event.latLng.lng()};
      marker2.setPosition(event.latLng);
      marker = new google.maps.Marker({
        position: {lat: eventLocation.lat, lng: eventLocation.lng},
        map: map3,
        title: 'Event'
    });

    

    });
  }catch(error){
    
  }// end add event map 


  //=====================================================================================================


  // Initialize add event responders map (MAP3)
  try{
    map3 = new google.maps.Map(document.getElementById('map3'), myOptions);

    // map.setCenter({lat: snap.data().location.latitude, lng: snap.data().location.longitude});
    
    var responderMarker2 = [];
    db.collection("Responders").get().then(function(querySnapshot) {
      querySnapshot.forEach(function(doc) {
        responderName = doc.data().username;

        var responder_loc = {
          'username': doc.data().username, 
          'lat': doc.data().location.latitude,
          'lng': doc.data().location.longitude,
          'avatarUrl': {
            url: doc.data().avatarUrl,
            size: new google.maps.Size(71, 71),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(50, 50)
          }
        };

        marker = new google.maps.Marker({
          position: {lat: responder_loc.lat, lng: responder_loc.lng},
          map: map3,
          title: responder_loc.username,
          icon: responder_loc.avatarUrl
        });
        responderMarker2.push(marker);
        
        respondersLoc.push({username: responderName, lat: responder_loc.lat, lng: responder_loc.lng});
        
      });
    });

    db.collection("Responders").onSnapshot(function(querySnapshot) {
      querySnapshot.docChanges.forEach(function(change) { 

        if (change.type == 'modified') {
          newLat = change.doc.data().location.latitude;
          newLng = change.doc.data().location.longitude;
          responderName = change.doc.data().username;

          function findResponder(responders) {
            return responders.getTitle() == responderName;
          }
          var idx = responderMarker2.findIndex(findResponder);
          responderMarker2[idx].setPosition(new google.maps.LatLng( newLat, newLng ));

          console.log("Changed: " + responderName);

          resNewDistanceEstimate = distanceMatrix({username: responderName, lat: newLat, lng: newLng}).then(function(resNewDistanceEstimate) {
            console.log(resNewDistanceEstimate);
            resDiv = resNewDistanceEstimate.username + '_distance';
            console.log(resDiv);
            resDistanceHTML = document.getElementById(resDiv);
            resDistanceHTML.innerHTML = resNewDistanceEstimate.text;

          }).catch(function(error) {
            console.log(error.message);
          });

        }
          


      });
    });

    
  }catch(error){
    // console.log(error);
  }//end add event responders map


  //-----------------------------------------------------------------------


  // Summary add event map (MAP4)
  try{
    map4 = new google.maps.Map(document.getElementById('map4'), myOptions);    
  }catch(error){
    // console.log(error);
  }//end add event responders map










  //-----------------------------------------------------------------------









  // Past events (MAP5)
  try{
    map5 = new google.maps.Map(document.getElementById('map5'), myOptions);
  }catch(error){
    // console.log(error);
  }//end add event responders map

  
}//end initMap




function distanceMatrix(coordinate) {

  return new Promise(function(resolve, reject) {

    respondersDistance = [];
    var service = new google.maps.DistanceMatrixService;
    var infoText;
    service.getDistanceMatrix({
      origins: [{lat: coordinate.lat, lng: coordinate.lng}],
      destinations: [{lat: eventLocation.lat, lng: eventLocation.lng}],
      travelMode: 'DRIVING',
      unitSystem: google.maps.UnitSystem.METRIC,
      avoidHighways: false,
      avoidTolls: false
    }, function(response, status) {
      if (status !== 'OK') {
        console.log('Error was: ' + status);
      } 
      else {
        for (var i = 0; i < response.originAddresses.length; i++) {
          result = response.rows[i].elements;
          for (var j = 0; j < result.length; j++) {
            if (result[j].status == 'OK') {
              distance = result[j].distance.text;
              duration = result[j].duration.text;
              infoText = "<small><b>Distance:</b> " + distance + " <b>Duration:</b></small>" + duration;
            }
            else {  
              infoText = "undefined";
            }
          }
        }
        resNameUnderscore = coordinate.username.replace(/ /g,"_");
        
        resInfoArray = {username: resNameUnderscore, text: infoText};
        respondersDistance.push(resInfoArray);

        if (resInfoArray) {
          console.log(resInfoArray);
          resolve(resInfoArray);
        }
        else {
          reject(new Error('Distance Matrix failed!'));
        }
        
      }

    });

  });
  
        respondersDistance.push({username: resNameUnderscore, text: "<small><b>Distance:</b> " + distance + " <b>Duration:</b></small>" + duration});
        // console.log(respondersDistance);
}


function UpdateCenter(map_name, lat, long){

  var mapToChange;

  if(map_name == "map3")
    mapToChange = map3;
  else if(map_name == "map4")
    mapToChange = map4;
  else if(map_name == "map5") 
    mapToChange = map5;  

    mapToChange.setCenter({lat: lat, lng: long});
}