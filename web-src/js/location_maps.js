"use strict";

var initMap;

(function () {
  //configuration values:
  var locationFormId = 'form-with-location';
  var locationFormFieldName = 'project_location';
  var mapCenter = {lat: 49.427357, lng: 32.074327};
  var mapZoom = 13;
  var selectionPolygon;

  var editableLocation = !window.appSingleProjectLocation && !window.appProjectsLocation;

  initMap = function () {

    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: mapZoom,
      center: mapCenter,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      streetViewControl: false
    });

    if (editableLocation) {
      var centerControlDiv = document.createElement('div');
      new CenterControl(centerControlDiv, map);
      centerControlDiv.index = 1;
      map.controls[google.maps.ControlPosition.TOP_LEFT].push(centerControlDiv);

      //info window
      var contentString = '<div id="content">'+
        '<div id="siteNotice">'+
        '</div>'+
        '<div id="bodyContent">'+
        '<p>Кликните на карте чтобы создать зону</p>'+
        '</div>'+
        '</div>';
      var infowindow = new google.maps.InfoWindow({
        content: contentString,
        position: mapCenter
      });
      infowindow.open(map);
      var eventListener = map.addListener('click', function (e) {
        infowindow.setMap(null);
        eventListener.remove();
      });

      // Construct the polygon.
      selectionPolygon = createPolygone([]);
      selectionPolygon.setOptions({
        draggable: true,
        editable: true
      });
      // add points
      map.addListener('click', function (e) {
        var newPoint = {
          lat: e.latLng.lat(),
          lng: e.latLng.lng()
        };
        var triangleCoords = getPaths(selectionPolygon);
        if (triangleCoords.length < 3) {
          triangleCoords.push(newPoint);
          selectionPolygon.setPaths(triangleCoords);
        } else {
          addBetweenClosestPoints(triangleCoords, newPoint);
        }
        document.querySelector('.customMapControl').style.display = 'block';
      });
    } else {
      if (window.appSingleProjectLocation) {
        createPolygone(appSingleProjectLocation);
      }
      if (window.appProjectsLocation) {
        appProjectsLocation.forEach(function (project) {
          project.polygon = createPolygone(project.location);
          project.polygon.addListener('click', function (e) {
            window.open(project.url, '_blank');
          });
        });

      }
    }

    function createPolygone (coords) {
      var polygon = new google.maps.Polygon({
        paths: coords,
        strokeColor: '#22b15c',
        strokeOpacity: 0.8,
        strokeWeight: 1.2,
        fillColor: '#22b15c',
        fillOpacity: 0.35
      });
      polygon.setMap(map);
      polygon.addListener('mouseover', function (e) {
        polygon.setOptions({fillOpacity: 0.5});
      });
      polygon.addListener('mouseout', function (e) {
        polygon.setOptions({fillOpacity: 0.35});
      });
      return polygon;
    }
    function addBetweenClosestPoints (triangleCoords, newPoint) {
      var nearestPointIndex = triangleCoords.indexOf(triangleCoords.map(function (point) {
        return {
          point: point,
          distance: getDistance(newPoint, point)
        };
      }).sort(function (o1, o2) {
        return o1.distance - o2.distance;
      })[0].point);
      var nearestPoint = triangleCoords[nearestPointIndex];
      var pointBeforeNearest = triangleCoords[nearestPointIndex - 1] || triangleCoords[triangleCoords.length - 1];
      var pointAfterNearest = triangleCoords[nearestPointIndex + 1] || triangleCoords[0];
      var segmentBeforeNearestPointAngle = getTwoSegmentsAngle(nearestPoint, pointBeforeNearest, newPoint);
      var segmentAfterNearestPointAngle = getTwoSegmentsAngle(nearestPoint, pointAfterNearest, newPoint);
      triangleCoords.splice(segmentBeforeNearestPointAngle < segmentAfterNearestPointAngle ? nearestPointIndex : nearestPointIndex + 1, 0, newPoint);
      selectionPolygon.setPaths(triangleCoords);
    }

    function getTwoSegmentsAngle(mutualPoint, point1, point2) {
      var angle = Math.abs(getSegmentAngle(mutualPoint, point1) - getSegmentAngle(mutualPoint, point2));
      return angle > 180 ? 360 - angle : angle;
    }

    function getSegmentAngle (start, end) {
      var x = end.lng - start.lng;
      var y = end.lat - start.lat;
      var theta = Math.atan(y/x)*180/Math.PI;
      if (x >= 0 && y >= 0) {
      } else if (x < 0 && y >= 0) {
        theta = 180 + theta;
      } else if (x < 0 && y < 0) {
        theta = 180 + theta;
      } else if (x > 0 && y < 0) {
        theta = 360 + theta;
      }
      return theta;
    }

    function getDistance (point1, point2) {
      var x = point1.lat - point2.lat;
      var y = point1.lng - point2.lng;
      return Math.sqrt(Math.pow(x,2) + Math.pow(y,2));
    }

    function CenterControl(controlDiv, map) {

      var controlUI = document.createElement('div');
      controlUI.title = 'Удалить созданную зону';
      controlUI.className = 'customMapControl';
      controlUI.style.display = 'none';
      controlDiv.appendChild(controlUI);

      var controlText = document.createElement('div');
      controlText.innerHTML = '<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Удалить зону';
      controlUI.appendChild(controlText);

      // Setup the click event listeners: simply set the map to Chicago.
      controlUI.addEventListener('click', function() {
        selectionPolygon.setPaths([]);
        document.querySelector('.customMapControl').style.display = 'none';
      });
    }
  };

  function getPaths (polygon) {
    var coords = [];
    polygon.getPaths().forEach(function (point) {
      if (typeof point.lat === 'function') {
        coords.push({
          lat: point.lat(),
          lng: point.lng()
        });
      } else {
        point.forEach(function (point) {
          coords.push({
            lat: point.lat(),
            lng: point.lng()
          });
        });
      }
    });
    return coords;
  }

  function polygonToJSON (polygon) {
    return JSON.stringify(getPaths(polygon));
  }

  if (editableLocation) {
    $('#' + locationFormId).submit(function (e) {
      $(e.target).find('input[name="'+ locationFormFieldName + '"]').val(polygonToJSON(selectionPolygon));
      return true;
    });
  }

})();