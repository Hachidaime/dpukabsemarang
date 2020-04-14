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

let genSegmentOld = () => {
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

let genSegment = () => {
    let coordinates = [];
    let importFilename = document.getElementById('upload_koordinat').value;
    let koordinat;
    if (importFilename != '') {
        importFilename = /*html*/`${server_base}/upload/temp/${importFilename}`;
        coordinates = getKML(importFilename);
    }
    else {
        let url = $table.bootstrapTable('getOptions').url;
        url = url.replace('search', 'searchori');
        koordinat = getAJAX(url);
        coordinates = JSON.parse(koordinat);
    }
    console.log(coordinates);
}

let getKML = importFilename => {
    // import the file --- se related function below
    let content;
    content = getAJAX(importFilename).toString();
    content = content.replace(/gx:/g, "");

    // build an xmlObj for parsing
    xmlDocObj = $($.parseXML(content));

    let coord;
    let coordinates = [];
    if (xmlDocObj.find('coordinates').length > 0) {
        coord = xmlDocObj.find('coordinates').html().trim().split(' ');
        coord.forEach(function (el) {
            let geo = [];
            el.split(',').forEach(function (row) {
                geo.push(parseFloat(row));
            })
            coordinates.push(geo);
        });
    }
    else {
        coord = xmlDocObj.find('coord');
        coord.each(function (i, el) {
            let geo = [];
            el.textContent.split(' ').forEach(function (row) {
                geo.push(parseFloat(row));
            });
            coordinates.push(geo);
        });
    }
    return coordinates;
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

let loadData = (map_data, type, jenis, simbol = null) => {
    features = new google.maps.Data();
    features.loadGeoJson(map_data);
    if (jenis != 'batas') {
        features.addListener('click', function (event) {
            // var myHTML = event.feature.getProperty("nama_jalan");
            // infowindow.setContent("<div style='width:300px;'>" + myHTML + "</div>");
            let myHTML = getFeatureInfo(event, jenis);
            infowindow.setContent(myHTML);

            // position the infowindow
            infowindow.setPosition(event.latLng);
            infowindow.open(map);
        });
    }
    features.setStyle(function (features) {
        switch (type) {
            case 'points':
                return ({
                    icon: {
                        url: `${server_base}/assets/img/${simbol}.png`,
                        scaledSize: new google.maps.Size(10, 10),
                        anchor: new google.maps.Point(5, 5),
                    },
                });
                break;
            case 'lines':
                return ({
                    fillColor: features.getProperty('fillColor'),
                    fillOpacity: features.getProperty('fillOpacity'),
                    strokeColor: features.getProperty('strokeColor'),
                    strokeWeight: features.getProperty('strokeWeight'),
                    strokeOpacity: features.getProperty('strokeOpacity'),
                });
                break;
            case 'border':
                let batasColors = { "Batas Kabupaten": "#0d0d0d", "Batas Kecamatan": "#808080", "Batas Desa": "#997300" };
                let batasWeight = { "Batas Kabupaten": "3", "Batas Kecamatan": "2", "Batas Desa": "1" };
                return ({
                    fillColor: batasColors[features.getProperty('fillColor')],
                    strokeColor: batasColors[features.getProperty('strokeColor')],
                    strokeWeight: batasWeight[features.getProperty('strokeWeight')],
                });
                break;
        }
    });
    features.setMap(map);

    return features;
}

let getFeatureInfo = (param, jenis) => {
    let type;
    let nomor;
    let nama;
    let segment;

    let html = [
        /*html*/`<div style="width:450px;">`,
        /*html*/`<table class="table table-bordered table-striped table-sm">`
    ];

    switch (jenis) {
        case 'jalan':
            type = "Ruas Jalan";
            nomor = param.feature.getProperty('no_jalan');
            nama = param.feature.getProperty('nama_jalan');
            break;
        case 'segment':
            type = "Ruas Jalan";
            nomor = param.feature.getProperty('no_jalan');
            nama = param.feature.getProperty('nama_jalan');
            segment = param.feature.getProperty('segment');
            break;
        case 'awal':
            type = "Ruas Jalan";
            nomor = param.feature.getProperty('no_jalan');
            nama = param.feature.getProperty('nama_jalan');
            break;
        case 'akhir':
            type = "Ruas Jalan";
            nomor = param.feature.getProperty('no_jalan');
            nama = param.feature.getProperty('nama_jalan');
            break;
        case 'jembatan':
            type = "Jembatan";
            break;
        case 'saluran':
            type = "Saluran Air";
            break;
        case 'gorong':
            type = "Gorong-gorong";
            break;
    }

    html.push(
        /*html*/`
        <tr>
            <td width="130px">No ${type}</td>
            <td width="*">${nomor}</td>
        </tr>
        `
    );

    html.push(
        /*html*/`
        <tr>
            <td>Nama ${type}</td>
            <td>${nama}</td>
        </tr>
        `
    );

    if (jenis == 'segment') {
        html.push(
            /*html*/`
            <tr>
                <td>Segment</td>
                <td>${segment}</td>
            </tr>
            `
        );
    }

    html.push(/*html*/`</table>`);
    html.push(/*html*/`</div>`);
    // console.log(html);
    return (html.join(''));
}

let Lines;

let loadLines = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${kepemilikan}.json`;
    Lines = loadData(map_data, 'lines', 'jalan');
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
    // kepemilikan = "JalanProvinsi";
    map_data = `${server_base}/data/JalanProvinsi.json`;
    JalanProvinsiLines = loadData(map_data, 'lines', 'jalan');
}

let loadComplete = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${kepemilikan}Complete.json`;
    CompleteLines = loadData(map_data, 'lines', 'jalan');
}

let loadPerkerasan = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${kepemilikan}Perkerasan.json`;
    PerkerasanLines = loadData(map_data, 'lines', 'jalan');
}

let loadKondisi = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${kepemilikan}Kondisi.json`;
    KondisiLines = loadData(map_data, 'lines', 'jalan');
}

let loadSegmentasi = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${kepemilikan}Segment.json`;
    SegmentasiPoints = loadData(map_data, 'points', 'segment', 'circle');
}

let loadAwal = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${kepemilikan}Awal.json`;
    AwalPoints = loadData(map_data, 'points', 'awal', 'triangle');
}

let loadAkhir = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${kepemilikan}Akhir.json`;
    AkhirPoints = loadData(map_data, 'points', 'akhir', 'rhombus');
}

let clearJalanProvinsi = () => {
    if (JalanProvinsiLines !== undefined) {
        JalanProvinsiLines.setMap(null);
    }
}

let clearComplete = () => {
    if (CompleteLines !== undefined) {
        CompleteLines.setMap(null);
    }
}

let clearPerkerasan = () => {
    if (PerkerasanLines !== undefined) {
        PerkerasanLines.setMap(null);
    }
}

let clearKondisi = () => {
    if (KondisiLines !== undefined) {
        KondisiLines.setMap(null);
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

let BatasLines;
let loadBatas = () => {
    map_data = `${server_base}/data/Batas.json`;
    BatasLines = loadData(map_data, 'border', 'batas');
}