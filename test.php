<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Places Searchbox</title>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #description {
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
      }

      #infowindow-content .title {
        font-weight: bold;
      }

      #infowindow-content {
        display: none;
      }

      #map #infowindow-content {
        display: inline;
      }

      .pac-card {
        margin: 10px 10px 0 0;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        background-color: #fff;
        font-family: Roboto;
      }

      #pac-container {
        padding-bottom: 12px;
        margin-right: 12px;
      }

      .pac-controls {
        display: inline-block;
        padding: 5px 11px;
      }

      .pac-controls label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
      }

      #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 400px;
      }

      #pac-input:focus {
        border-color: #4d90fe;
      }

      #title {
        color: #fff;
        background-color: #4d90fe;
        font-size: 25px;
        font-weight: 500;
        padding: 6px 12px;
      }
      #target {
        width: 345px;
      }
    </style>
  </head>
  <body>
  <?php
        //PHP code is adapted from John Phillips' work
        $DB_USER = 'tenniesjd13';
        $DB_PASSWORD = 'tenniesjd13';

        //connect to the database
        try {
                $dbh = new PDO('mysql:host=localhost;dbname=tenniesjd13;charset=utf8', $DB_USER, $DB_PASSWORD);
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
                echo "Error!: " . $e->getMessage() . "<br>";
                die();
        }
        
        //executed when the form is submitted
        if(isset($_POST['submit'])) {
                $name = "";
                $description = "";
                $latitude = "";
                $longitude = "";
                $zoom = "";

                //checks each field to make sure they are not null and have a length > 0
                if(isset($_POST['name'], $_POST['description'], $_POST['latitude'],
                        $_POST['longitude'], $_POST['zoom']) && strlen($_POST['name']) > 0
                        && strlen($_POST['description']) > 0 && strlen($_POST['latitude']) > 0 
                        && strlen($_POST['longitude']) > 0 && strlen($_POST['zoom']) > 0) 
                {
                        //sanitizes the given variable
                        $name = $_POST['name'];
                        $name = trim($name);
                        $name = stripslashes($name);
                        $name = htmlspecialchars($name);
                        $name = preg_replace('/\n/', '', $name);

                        $description = $_POST['description'];
                        $description = trim($description);
                        $description = stripslashes($description);
                        $description = htmlspecialchars($description);
                        $description = preg_replace('/\n/', '', $description);

                        $latitude = $_POST['latitude'];
                        $latitude = trim($latitude);
                        $latitude = stripslashes($latitude);
                        $latitude = htmlspecialchars($latitude);
                        $latitude = preg_replace('/\n/', '', $latitude);

                        $longitude = $_POST['longitude'];
                        $longitude = trim($longitude);
                        $longitude = stripslashes($longitude);
                        $longitude = htmlspecialchars($longitude);
                        $longitude = preg_replace('/\n/', '', $longitude);

                        $zoom = $_POST['zoom'];
                        $zoom = trim($zoom);
                        $zoom = stripslashes($zoom);
                        $zoom = htmlspecialchars($zoom);
                        $zoom = preg_replace('/\n/', '', $zoom);

                        try {
                                $q = "insert into locations (id, name, description, latitude, longitude, zoomLevel) 
                                        values (null, ?, ?, ?, ?, ?)";
                                $stmt = $dbh->prepare($q);
                                $stmt->bindParam(1, $name); //bindParam prepares each parameter 
                                $stmt->bindParam(2, $description);
                                $stmt->bindParam(3, $latitude);
                                $stmt->bindParam(4, $longitude);
                                $stmt->bindParam(5, $zoom);
                                $stmt->execute();
                        } catch (PDOException $e) {
                                echo "<p>Error!: " . $e->getMessage() . "</p>";
                        }
                        
                } else { //sends an alert that there is an error in the field
                        $error = "There is an error in the form. Please correct it and try again.";
                        echo "<script>alert('$error');</script>";
                }
        }
        //here is our SQL query statement
        $q = "select * from locations";

        $i = 0;
        //execute query and display the results
        foreach($dbh->query($q) as $row) {
                echo "$row[id], $row[description], $row[name], $row[latitude], $row[longitude], $row[zoomLevel]\n";
        }

         
        ?>
    <input id="pac-input" class="controls" type="text" placeholder="Search Box">
    <div id="map"></div>
    <form method="post" name="addLocation">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name"/>
                <label for="description">Description:</label>
                <input type="text" id="description" name="description"/>
                <label for="latitude">Lat:</label>
                <input type="text" id="latitude" name="latitude" readonly/>
                <label for="longitude">Lng:</label>
                <input type="text" id="longitude" name="longitude" readonly/>
                <label for="zoom">Zoom:</label>
                <input type="text"  id="zoom" name="zoom" readonly/>
                <button type="submit" name="submit" value="Submit Location">Submit</button>
                <button type ="reset">Reset</button>
        </form>
    <script>
      // This example adds a search box to a map, using the Google Place Autocomplete
      // feature. People can enter geographical searches. The search box will return a
      // pick list containing a mix of places and predicted search terms.

      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

      function initAutocomplete() {
        // var map = new google.maps.Map(document.getElementById('map'), {
        //   center: {lat: -33.8688, lng: 151.2195},
        //   zoom: 13,
        //   mapTypeId: 'roadmap'
        // });
                        var mapProp = {
                                center: new google.maps.LatLng(53.2734, -7.7783),
                                zoom: 8,
                                panControl: true,
                                zoomControl: true,
                                mapTypeControl: true,
                                scaleControl: true,
                                streetViewControl: true,
                                overviewMapControl: true,
                                rotateControl: true,
                                mapTypeId: "satellite",
                        };
                        map = new google.maps.Map(document.getElementById("map"), mapProp);

                        


        // Create the search box and link it to the UI element.
        var input = document.getElementById('pac-input');
        var searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function() {
          searchBox.setBounds(map.getBounds());
        });
        
        map.addListener('click', function (e) {
                                // placeMarkerFillForm(map, e.latLng);
                                map.setCenter(e.latLng);
                        });
                

        var markers = [];
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function() {
          var places = searchBox.getPlaces();

          if (places.length == 0) {
            return;
          }

          // Clear out the old markers.
          markers.forEach(function(marker) {
            marker.setMap(null);
          });
          markers = [];

          // For each place, get the icon, name and location.
          var bounds = new google.maps.LatLngBounds();
          places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }
            var icon = {
              url: place.icon,
              size: new google.maps.Size(71, 71),
              origin: new google.maps.Point(0, 0),
              anchor: new google.maps.Point(17, 34),
              scaledSize: new google.maps.Size(25, 25)
            };

            // Create a marker for each place.
            markers.push(new google.maps.Marker({
              map: map,
              icon: icon,
              title: place.name,
              position: place.geometry.location
            }));

            if (place.geometry.viewport) {
              // Only geocodes have viewport.
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
          });
          map.fitBounds(bounds);
        });
      }
      //grabs the locations and adds an event listener to view that location on maps
                var entries = document.getElementById("showlist");
                entries.addEventListener('click', viewLocation, false);

                //displays the locations inside a div
                var list = document.getElementById("list").innerHTML;
                var selections = list.split("\n");
                for(var i = 0; i < selections.length; i++) {
                        var p = document.createElement("p");
                        p.innerHTML = selections[i];
                        entries.appendChild(p);
                }
                

                function viewLocation(e) {
                        var clickedLocation;
                        if (e.target !== e.currentTarget) {
                                clickedLocation = e.target.innerHTML;
                        }
                        e.stopPropagation();
                        var data = clickedLocation.split(", ");
                        map.setCenter(new google.maps.LatLng(data[3], data[4]));
                        map.setZoom(parseInt(data[5]));
                }

                //deletes old marker if applicable, places new one, and auto populates the latitude, longitude, and zoom
                //fields for the user, helps prevent injections
                function placeMarkerFillForm(map, location) {
                        markers.forEach(function(marker) {
                            marker.setMap(null);
                        });
                         markers = [];
                        var marker = new google.maps.Marker({
                                position: location,
                                map: map,
                                animation: google.maps.Animation.DROP,
                        });
                        markers = [];
                        markers.push(marker);
                        markers[0].setMap(map);   

                        var lat = location.lat().toFixed(6);
                        var lng = location.lng().toFixed(6);
                        var zoom = map.getZoom();

                        //fills the latitude, longitude, and zoom of the form, elements that are not editable any other way
                        document.getElementById("latitude").value = lat;
                        document.getElementById("longitude").value = lng;
                        document.getElementById("zoom").value = zoom;
                }

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDzEXkWBv5UwBou6YyYiiKlMCsz4sjBuiM&libraries=places&callback=initAutocomplete"
         async defer></script>
  </body>
</html>