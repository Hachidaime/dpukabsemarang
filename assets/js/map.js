/**
 * assets/js/map.js
 */

/**
 * * Mendefinisikan variable
 */
var center = null;
var currentPopup;
var bounds = new google.maps.LatLngBounds();
var infowindow = new google.maps.InfoWindow()

/**
 * * Inisiasi Map (menampilkan map pada layar)
 * */
let initMap = () => {
    /**
     * * Map Options
     */
    map = new google.maps.Map(document.getElementById("map_canvas"), {
        center: new google.maps.LatLng(DEFAULT_LATITUDE, DEFAULT_LONGITUDE),
        gestureHandling: 'greedy',
        zoom: 11,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControl: true,
        mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
            position: google.maps.ControlPosition.TOP_RIGHT
        },
        fullscreenControl: false,
        navigationControl: true,
        navigationControlOptions: {
            style: google.maps.NavigationControlStyle.SMALL
        },
        zoomControlOptions: {
            position: google.maps.ControlPosition.RIGHT_CENTER
        },
        streetViewControlOptions: {
            position: google.maps.ControlPosition.RIGHT_BOTTOM
        }
    });
    center = bounds.getCenter();

    // Create the DIV to hold the control and call the CenterControl()
    // constructor passing in this DIV.
    let controlCentering = document.createElement('div');
    let centering = new centerControl(controlCentering, map);
    // controlDiv.index = 1;
    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(controlCentering);

    let controlNav = document.createElement('div');
    let myNav = new controlOpenNav(controlNav, map);
    // controlDiv.index = 1;
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(controlNav);

    map.data.addListener('click', function (event) {
        var myHTML = event.feature.getProperty("nama_jalan");
        infowindow.setContent("<div style='width:150px;'>" + myHTML + "</div>");
        // position the infowindow
        infowindow.setPosition(event.latLng);
        infowindow.open(map);
    });

    return map;
}

let makeControl = () => {
    let controlUI = document.createElement('div');
    controlUI.style.backgroundColor = '#fff';
    controlUI.style.border = '2px solid #fff';
    controlUI.style.borderRadius = '2px';
    controlUI.style.boxShadow = 'rgba(0, 0, 0, 0.3) 0px 1px 4px -1px';
    controlUI.style.cursor = 'pointer';
    controlUI.style.margin = '10px';
    controlUI.style.textAlign = 'center';

    return controlUI;
}

function centerControl(controlDiv, map) {

    // Set CSS for the control border.
    let controlUI = makeControl();
    controlUI.title = 'Click to recenter the map';
    controlDiv.appendChild(controlUI);

    // Set CSS for the control interior.
    let controlText = document.createElement('div');
    controlText.style.color = 'rgb(25,25,25)';
    controlText.style.fontSize = '16px';
    controlText.style.lineHeight = '12px';
    controlText.style.padding = '5px';
    controlText.innerHTML = /*html*/`<i class="material-icons">filter_center_focus</i>`;
    controlUI.appendChild(controlText);

    // Setup the click event listeners: simply set the map to Chicago.
    controlUI.addEventListener('click', function () {
        map.setCenter({ lat: DEFAULT_LATITUDE, lng: DEFAULT_LONGITUDE });
    });
}

function controlOpenNav(controlDiv, map) {

    // ? Set CSS for the control border.
    let controlUI = makeControl();
    controlUI.title = 'Click to show navigation';
    controlDiv.appendChild(controlUI);

    // ? Set CSS for the control interior.
    let controlText = document.createElement('div');
    controlText.style.color = 'rgb(25,25,25)';
    controlText.style.fontSize = '16px';
    controlText.style.lineHeight = '12px';
    controlText.style.padding = '5px';
    controlText.innerHTML = /*html*/`<i class="material-icons">more_vert</i>`;
    controlUI.appendChild(controlText);

    // ? Setup the click event listeners
    controlUI.addEventListener('click', function () {
        // TODO: Open sidenav
        openNav();
    });

}

let makeCoordinateArray = koordinat => {
    let coord = []
    $(koordinat).each(function (k, i) {
        coord.push([i.latitude, i.longitude]);
    });
    return coord;
}

let makeCoordinatArrayObject = koordinat => {
    let coordinates = [];
    $(koordinat).each(function (k, i) {
        coordinates.push(new google.maps.LatLng(i.latitude, i.longitude));
    });
    return coordinates;
}

let makePath = coordinates => {
    return new google.maps.Polyline({
        path: coordinates
    });
}

let countLength = path => google.maps.geometry.spherical.computeLength(path.getPath());

let getSegment = (path, coord, segmentasi, remainingDist) => {
    var coordSegment = [];
    if (segmentasi > 0) {
        var i = 1;
        while (remainingDist > 0) {
            var point = path.GetPointAtDistance(segmentasi * i);
            if (point != null) {
                coordSegment.push([point.lat(), point.lng()]);
            }
            remainingDist -= segmentasi;
            i++;
        }

        $(coordSegment).each(function (k, i) {
            var line = turf.lineString(coord);
            var pt = turf.point(i);
            var snapped = turf.nearestPointOnLine(line, pt);

            coordSegment[k].push(snapped.properties.index);
        });
    }

    return coordSegment;
}

let genSegment = () => {
    let url = $table.bootstrapTable('getOptions').url;
    url = url.replace('search', 'searchori');
    let segmentasi = $('#segmentasi').val();
    let file = $('#upload_koordinat').val();
    let params = {};
    params['file'] = file;
    $.post(url, $.param(params), function (data) {
        let koordinat = data;
        let coord = makeCoordinateArray(koordinat);
        let coordinates = makeCoordinatArrayObject(koordinat);
        let roadPath = makePath(coordinates);
        let roadLength = countLength(roadPath);
        let coordSegment = getSegment(roadPath, coord, segmentasi, roadLength);

        $('#panjang').val(roadLength.toFixed(2));
        $('#panjang_text').val(roadLength.toFixed(2));
        url = url.replace('searchori', 'setsession');
        let params = {};
        params['coordsegment'] = coordSegment;
        params['coord'] = coord;

        $.post(url, $.param(params), function () {
            $table.bootstrapTable('refresh')
        });
    }, "json");
}

let getKepemilikan = () => {
    let kepemilikan = document.getElementById('kepemilikan').value;
    if (kepemilikan != 0) {
        switch (kepemilikan) {
            case '2':
                kepemilikan = 'JalanKotaKabupaten';
                break;
            case '3':
                kepemilikan = 'JalanPorosDesa';
                break;
            default:
                kepemilikan = 'JalanSemua';
                break;
        }
    }

    return kepemilikan;
}

let Lines;

let loadLines = () => {
    kepemilikan = getKepemilikan();

    map_data = `${server_base}/data/${active_data_dir}/${kepemilikan}.json`;

    $.getJSON(map_data, function (data) {
        Lines = new google.maps.Data();
        Lines.addGeoJson(data);
        Lines.setStyle(function (features) {
            return /** @type {google.maps.Data.StyleOptions} */({
                fillColor: features.getProperty('fillColor'),
                fillOpacity: features.getProperty('fillOpacity'),
                strokeColor: features.getProperty('strokeColor'),
                strokeWeight: features.getProperty('strokeWeight'),
                strokeOpacity: features.getProperty('strokeOpacity'),
            });
        });
        Lines.setMap(map);
    });
}

let clearLines = () => {
    if (Lines !== undefined) {
        Lines.setMap(null);
    }
}

let JalanProvinsiLines;
let CompleteLines;
let PerkerasanLines;
let KondisiLines;
let SegmentasiPoints;
let AwalPoints;
let AkhirPoints;

let loadSwitch = () => {
    let jlnProvinsi = document.getElementById('jalan_provinsi').checked;
    if (jlnProvinsi) {
        loadJalanProvinsi();
    }

    let perkerasan = document.getElementById('perkerasan').checked;
    let kondisi = document.getElementById('kondisi').checked;

    if (perkerasan && kondisi) {
        clearPerkerasan();
        clearKondisi();
        loadComplete();
    }
    else {
        clearComplete();

        if (perkerasan) {
            loadPerkerasan()
        }
        else {
            clearPerkerasan();
        }

        if (kondisi) {
            loadKondisi();
        }
        else {
            clearKondisi();
        }
    }
}

let loadJalanProvinsi = () => {
    kepemilikan = "JalanProvinsi";
    map_data = `${server_base}/data/${active_data_dir}/${kepemilikan}.json`;
    $.getJSON(map_data, function (data) {
        JalanProvinsiLines = new google.maps.Data();
        JalanProvinsiLines.addGeoJson(data);
        JalanProvinsiLines.setStyle(function (features) {
            return /** @type {google.maps.Data.StyleOptions} */({
                fillColor: features.getProperty('fillColor'),
                fillOpacity: features.getProperty('fillOpacity'),
                strokeColor: features.getProperty('strokeColor'),
                strokeWeight: features.getProperty('strokeWeight'),
                strokeOpacity: features.getProperty('strokeOpacity'),
            });
        });
        JalanProvinsiLines.setMap(map);
    });
}

let loadComplete = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${active_data_dir}/${kepemilikan}Complete.json`;
    $.getJSON(map_data, function (data) {
        CompleteLines = map.data.addGeoJson(data);
        setLineStyle(CompleteLines);
    });
}

let loadPerkerasan = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${active_data_dir}/${kepemilikan}Perkerasan.json`;
    $.getJSON(map_data, function (data) {
        PerkerasanLines = map.data.addGeoJson(data);
        setLineStyle(PerkerasanLines);
    });
}

let loadKondisi = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${active_data_dir}/${kepemilikan}Kondisi.json`;
    $.getJSON(map_data, function (data) {
        KondisiLines = map.data.addGeoJson(data);
        setLineStyle(KondisiLines);
    });
}

let loadSegmentasi = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${active_data_dir}/${kepemilikan}Segment.json`;
    SegmentasiPoints = new google.maps.Data();
    SegmentasiPoints.loadGeoJson(map_data);
    SegmentasiPoints.addListener('click', function (event) {
        var myHTML = event.feature.getProperty("nama_jalan");
        infowindow.setContent("<div style='width:150px;'>" + myHTML + "</div>");
        // position the infowindow
        infowindow.setPosition(event.latLng);
        infowindow.open(map);
    });
    SegmentasiPoints.setStyle(function () {
        return ({
            icon: {
                url: `${server_base}/assets/img/circle.png`,
                scaledSize: new google.maps.Size(10, 10),
                anchor: new google.maps.Point(5, 5),
            },
        });
    });
    SegmentasiPoints.setMap(map);
}

let loadAwal = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${active_data_dir}/${kepemilikan}Awal.json`;
    AwalPoints = new google.maps.Data();
    AwalPoints.loadGeoJson(map_data);
    AwalPoints.addListener('click', function (event) {
        var myHTML = event.feature.getProperty("nama_jalan");
        infowindow.setContent("<div style='width:150px;'>" + myHTML + "</div>");
        // position the infowindow
        infowindow.setPosition(event.latLng);
        infowindow.open(map);
    });
    AwalPoints.setStyle(function () {
        return ({
            icon: {
                url: `${server_base}/assets/img/triangle.png`,
                scaledSize: new google.maps.Size(12, 12),
                anchor: new google.maps.Point(6, 6),
            },
        });
    });
    AwalPoints.setMap(map);
}

let loadAkhir = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${active_data_dir}/${kepemilikan}Akhir.json`;
    AkhirPoints = new google.maps.Data();
    AkhirPoints.loadGeoJson(map_data);
    AkhirPoints.addListener('click', function (event) {
        var myHTML = event.feature.getProperty("nama_jalan");
        infowindow.setContent("<div style='width:150px;'>" + myHTML + "</div>");
        // position the infowindow
        infowindow.setPosition(event.latLng);
        infowindow.open(map);
    });
    AkhirPoints.setStyle(function () {
        return ({
            icon: {
                url: `${server_base}/assets/img/rhombus.png`,
                scaledSize: new google.maps.Size(12, 12),
                anchor: new google.maps.Point(6, 6),
            },
        });
    });
    AkhirPoints.setMap(map);
}

let clearJalanProvinsi = () => {
    if (JalanProvinsiLines !== undefined) {
        Lines.setMap(null);
    }
}

let clearComplete = () => {
    if (CompleteLines !== undefined) {
        for (var i = 0; i < CompleteLines.length; i++) {
            map.data.remove(CompleteLines[i]);
        }
    }
}

let clearPerkerasan = () => {
    if (PerkerasanLines !== undefined) {
        for (var i = 0; i < PerkerasanLines.length; i++) {
            map.data.remove(PerkerasanLines[i]);
        }
    }
}

let clearKondisi = () => {
    if (KondisiLines !== undefined) {
        for (var i = 0; i < KondisiLines.length; i++) {
            map.data.remove(KondisiLines[i]);
        }
    }
}

let clearSegmentasi = () => {
    if (SegmentasiPoints !== undefined) {
        SegmentasiPoints.setMap(null);
    }
}

let clearAwal = () => {
    if (AwalPoints !== undefined) {
        AwalPoints.setMap(null);
    }
}

let clearAkhir = () => {
    if (AkhirPoints !== undefined) {
        AkhirPoints.setMap(null);
    }
}