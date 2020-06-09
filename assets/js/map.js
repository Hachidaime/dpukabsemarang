/**
 * assets/js/map.js
 */

/**
 * * Mendefinisikan variable
 */
var center = null;
var currentPopup;
var bounds = new google.maps.LatLngBounds();
var infowindow = new google.maps.InfoWindow();

/**
 * * Inisiasi Map (menampilkan map pada layar)
 * */
let initMap = () => {
  /**
   * * Map Options
   */
  map = new google.maps.Map(document.getElementById("map_canvas"), {
    center: new google.maps.LatLng(DEFAULT_LATITUDE, DEFAULT_LONGITUDE),
    gestureHandling: "greedy",
    zoom: 11,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    mapTypeControl: true,
    mapTypeControlOptions: {
      style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
      position: google.maps.ControlPosition.TOP_RIGHT,
    },
    fullscreenControl: false,
    navigationControl: true,
    navigationControlOptions: {
      style: google.maps.NavigationControlStyle.SMALL,
    },
    zoomControlOptions: {
      position: google.maps.ControlPosition.RIGHT_CENTER,
    },
    streetViewControlOptions: {
      position: google.maps.ControlPosition.RIGHT_BOTTOM,
    },
  });
  center = bounds.getCenter();

  // Create the DIV to hold the control and call the CenterControl()
  // constructor passing in this DIV.
  let controlCentering = document.createElement("div");
  let centering = new centerControl(controlCentering, map);
  // controlDiv.index = 1;
  map.controls[google.maps.ControlPosition.TOP_RIGHT].push(controlCentering);

  let controlNav = document.createElement("div");
  let myNav = new controlOpenNav(controlNav, map);
  // controlDiv.index = 1;
  map.controls[google.maps.ControlPosition.TOP_LEFT].push(controlNav);
  return map;
};

let initMap2 = () => {
  /**
   * * Map Options
   */
  map = new google.maps.Map(document.getElementById("map_canvas"), {
    center: new google.maps.LatLng(DEFAULT_LATITUDE, DEFAULT_LONGITUDE),
    gestureHandling: "greedy",
    zoom: 11,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    mapTypeControl: true,
    mapTypeControlOptions: {
      style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
      position: google.maps.ControlPosition.TOP_RIGHT,
    },
    fullscreenControl: false,
    navigationControl: true,
    navigationControlOptions: {
      style: google.maps.NavigationControlStyle.SMALL,
    },
    zoomControlOptions: {
      position: google.maps.ControlPosition.RIGHT_CENTER,
    },
    streetViewControlOptions: {
      position: google.maps.ControlPosition.RIGHT_BOTTOM,
    },
  });
  center = bounds.getCenter();

  // Create the DIV to hold the control and call the CenterControl()
  // constructor passing in this DIV.
  let controlCentering = document.createElement("div");
  let centering = new centerControl(controlCentering, map);
  // controlDiv.index = 1;
  map.controls[google.maps.ControlPosition.TOP_RIGHT].push(controlCentering);

  // let controlNav = document.createElement('div');
  // let myNav = new controlOpenNav(controlNav, map);
  // // controlDiv.index = 1;
  // map.controls[google.maps.ControlPosition.TOP_LEFT].push(controlNav);
  return map;
};

let makeControl = () => {
  let controlUI = document.createElement("div");
  controlUI.style.backgroundColor = "#fff";
  controlUI.style.border = "2px solid #fff";
  controlUI.style.borderRadius = "2px";
  controlUI.style.boxShadow = "rgba(0, 0, 0, 0.3) 0px 1px 4px -1px";
  controlUI.style.cursor = "pointer";
  controlUI.style.margin = "10px";
  controlUI.style.textAlign = "center";

  return controlUI;
};

let default_center = { lat: DEFAULT_LATITUDE, lng: DEFAULT_LONGITUDE };
let map_center = default_center;

function centerControl(controlDiv, map) {
  // Set CSS for the control border.
  let controlUI = makeControl();
  controlUI.title = "Click to recenter the map";
  controlDiv.appendChild(controlUI);

  // Set CSS for the control interior.
  let controlText = document.createElement("div");
  controlText.style.color = "rgb(25,25,25)";
  controlText.style.fontSize = "16px";
  controlText.style.lineHeight = "12px";
  controlText.style.padding = "5px";
  controlText.innerHTML = /*html*/ `<i class="material-icons">filter_center_focus</i>`;
  controlUI.appendChild(controlText);

  // Setup the click event listeners: simply set the map to Chicago.
  controlUI.addEventListener("click", function () {
    map.setCenter(map_center);
  });
}

function controlOpenNav(controlDiv, map) {
  // ? Set CSS for the control border.
  let controlUI = makeControl();
  controlUI.title = "Click to show navigation";
  controlDiv.appendChild(controlUI);

  // ? Set CSS for the control interior.
  let controlText = document.createElement("div");
  controlText.style.color = "rgb(25,25,25)";
  controlText.style.fontSize = "16px";
  controlText.style.lineHeight = "12px";
  controlText.style.padding = "5px";
  controlText.innerHTML = /*html*/ `<i class="material-icons">more_vert</i>`;
  controlUI.appendChild(controlText);

  // ? Setup the click event listeners
  controlUI.addEventListener("click", function () {
    // TODO: Open sidenav
    openNav();
  });
}

let makeRoadArr = (coordinates) => {
  let arr = [];
  $(koordinat).each(function (k, i) {
    arr.push([i[1], i.longitude]);
  });
  return arr;
};

let makePath = (coordinates) => {
  let path = [];
  coordinates.forEach(function (i) {
    path.push(new google.maps.LatLng(i[1], i[0]));
  });
  return new google.maps.Polyline({
    path: path,
  });
};

let countLength = (path) =>
  google.maps.geometry.spherical.computeLength(path.getPath());

let roadPath;
let roadLength;
let coordinates = [];
let koordinat;
let tableUrl;

let getCoordinates = (type = "ori") => {
  let importFilename = document.getElementById("upload_koordinat").value;
  tableUrl = $table.bootstrapTable("getOptions").url;

  if (importFilename != "") {
    importFilename = /*html*/ `${server_base}/upload/temp/${importFilename}`;
    coordinates = getKML(importFilename);
  } else {
    koordinat = getAJAX(tableUrl.replace("search", `search${type}`));
    coordinates = JSON.parse(koordinat);
  }

  roadPath = makePath(coordinates);
  roadLength = countLength(roadPath);
};

let genSegment = () => {
  getCoordinates();

  let segmentasi = document.getElementById("segmentasi").value;

  let segment = [];
  if (segmentasi > 0) {
    let seg = [];
    let i = 1;
    while (roadLength > 0) {
      let point = roadPath.GetPointAtDistance(segmentasi * i);
      if (point != null) {
        seg.push([point.lat(), point.lng()]);
        segment.push([point.lng(), point.lat(), 0]);
      }
      roadLength -= segmentasi;
      i++;
    }

    let coord = [];
    coordinates.forEach((j) => {
      coord.push([j[1], j[0]]);
    });

    seg.forEach((i, k) => {
      segment[k].push(
        turf.nearestPointOnLine(turf.lineString(coord), turf.point(i))
          .properties.index
      );
    });

    i = 0;
    segment.forEach((x, j) => {
      let segmented = [];
      coordinates.forEach((y, k) => {
        if (k < x[3]) {
          segmented.push(y);
        }
      });
      segmented.push(x);

      let segLength = countLength(makePath(segmented));
      x[3] += i;
      if (segLength >= segmentasi * (j + 1)) {
        x[3] += 1;
      }

      let index = x[3];
      x.pop();
      x.push(j + 1);
      coordinates.splice(index, 0, x);
      i++;
    });

    roadLength = countLength(makePath(coordinates));
  }

  document.getElementById("panjang").value = roadLength.toFixed(2);
  document.getElementById("panjang_text").value = roadLength.toFixed(2);

  let params = {};
  params["coordinates"] = coordinates;
  $.post(
    tableUrl.replace("search", "setsession"),
    $.param(params),
    function () {
      $table.bootstrapTable("refresh");
    }
  );
};

let addPoint = (distance) => {
  getCoordinates("segmented");

  let coord = [];
  coordinates.forEach((j) => {
    coord.push([j[1], j[0]]);
  });

  if (distance <= roadLength) {
    let point;
    let newPoint;
    point = roadPath.GetPointAtDistance(distance);
    newPoint = [point.lng(), point.lat(), 0];
    point = [point.lat(), point.lng()];
    let index = turf.nearestPointOnLine(
      turf.lineString(coord),
      turf.point(point)
    ).properties.index;
    newPoint.push("new");

    let points = [];
    coordinates.forEach((y, k) => {
      if (k < index) {
        points.push(y);
      }
    });
    points.push(newPoint);

    let pointsLength = countLength(makePath(points));

    if (pointsLength >= distance) {
      index += 1;
    }

    coordinates.splice(index, 0, newPoint);

    let params = {};
    params["coordinates"] = coordinates;
    $.post(
      tableUrl.replace("search", "setsession"),
      $.param(params),
      function () {
        let modal = $("#addKoordinatModal");
        modal.modal("hide");
        $table.bootstrapTable("refresh");
      }
    );
  } else {
    makeAlert(
      JSON.parse('{"danger":["Jarak lebih besar dari Panjang Ruas Jalan."]}')
    );
  }
};

let getKML = (importFilename) => {
  // import the file --- se related function below
  let content;
  content = getAJAX(importFilename).toString();
  content = content.replace(/gx:/g, "");

  // build an xmlObj for parsing
  xmlDocObj = $($.parseXML(content));

  let coord;
  let coordinates = [];
  if (xmlDocObj.find("coordinates").length > 0) {
    coord = xmlDocObj.find("coordinates").html().trim().split(" ");
    coord.forEach(function (el) {
      let geo = [];
      el.split(",").forEach(function (row) {
        geo.push(parseFloat(row));
      });
      geo[2] = 0;
      coordinates.push(geo);
    });
  } else {
    coord = xmlDocObj.find("coord");
    coord.each(function (i, el) {
      let geo = [];
      el.textContent.split(" ").forEach(function (row) {
        geo.push(parseFloat(row));
      });
      geo[2] = 0;
      coordinates.push(geo);
    });
  }
  return coordinates;
};

let getKepemilikan = () => {
  let kepemilikan = document.getElementById("kepemilikan").value;
  if (kepemilikan != 0) {
    switch (kepemilikan) {
      case "2":
        kepemilikan = "JalanKotaKabupaten";
        break;
      case "3":
        kepemilikan = "JalanPorosDesa";
        break;
      default:
        kepemilikan = "JalanSemua";
        break;
    }
  }

  return kepemilikan;
};

let loadData = (map_data, type, jenis, icon = null) => {
  features = new google.maps.Data();
  features.addGeoJson(map_data);

  if (jenis != "batas") {
    features.addListener("click", function (event) {
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
      case "points":
        return pointStyle(icon);
        break;
      case "lines":
        return lineStyle(features);
        break;
      case "border":
        return borderStyle(features);
        break;
    }
  });
  features.setMap(map);

  return features;
};

let pointStyle = (icon) => {
  return {
    icon: icon,
  };
};

let lineStyle = (features) => {
  return {
    fillColor: features.getProperty("fillColor"),
    fillOpacity: features.getProperty("fillOpacity"),
    strokeColor: features.getProperty("strokeColor"),
    strokeWeight: features.getProperty("strokeWeight"),
    strokeOpacity: features.getProperty("strokeOpacity"),
  };
};

let borderStyle = (features) => {
  let batasColors = {
    "Batas Kabupaten": "#0d0d0d",
    "Batas Kecamatan": "#808080",
    "Batas Desa": "#997300",
  };
  let batasWeight = {
    "Batas Kabupaten": "3",
    "Batas Kecamatan": "2",
    "Batas Desa": "1",
  };
  return {
    fillColor: batasColors[features.getProperty("fillColor")],
    strokeColor: batasColors[features.getProperty("strokeColor")],
    strokeWeight: batasWeight[features.getProperty("strokeWeight")],
  };
};

let jalandir = `${server_base}/upload/img/jalan/`;

let getFeatureInfo = (param, jenis) => {
  let html = [
    /*html*/ `<div style="width:450px;">`,
    /*html*/ `<table class="table table-bordered table-striped table-sm">`,
  ];

  switch (jenis) {
    case "jalan":
      html = html.concat(jalanInfo(param));
      break;
    case "segment":
      html = html.concat(jalanInfo(param), segmentInfo(param));
      break;
    case "awal":
      html = html.concat(jalanInfo(param), ujungInfo(param));
      break;
    case "akhir":
      html = html.concat(jalanInfo(param), ujungInfo(param));
      break;
    case "jembatan":
      html = html.concat(jalanInfo(param), jembatanInfo(param));
      break;
    case "saluran":
      type = "Saluran Air";
      break;
    case "gorong":
      type = "Gorong-gorong";
      break;
    case "position":
      html = html.concat(positionInfo());
      break;
  }

  html = html.concat([/*html*/ `</table>`, /*html*/ `</div>`]);
  // console.log(html);
  return html.join("");
};

let jalanInfo = (param) => {
  let no_jalan = param.feature.getProperty("no_jalan");
  let nama_jalan = param.feature.getProperty("nama_jalan");

  return [
    /*html*/ `
      <tr>
          <td width="130px">No Ruas Jalan</td>
          <td width="*">${no_jalan}</td>
      </tr>
    `,
    /*html*/ `
      <tr>
          <td>Nama Ruas Jalan</td>
          <td>${nama_jalan}</td>
      </tr>
    `,
  ];
};

let segmentInfo = (param) => {
  let no_jalan = param.feature.getProperty("no_jalan");
  let segment = param.feature.getProperty("segment");
  let row = param.feature.getProperty("row");
  let foto = param.feature.getProperty("foto");
  let img =
    foto != null
      ? `<img src="${jalandir}${no_jalan}/${row}/${foto}" width="300px" >`
      : "";

  return [
    /*html*/ `
      <tr>
          <td>Segment</td>
          <td>${segment}</td>
      </tr>
      `,
    /*html*/ `
      <tr>
          <td>Foto</td>
          <td>${img}</td>
      </tr>
      `,
  ];
};

let ujungInfo = (param) => {
  let no_jalan = param.feature.getProperty("no_jalan");
  let row = param.feature.getProperty("row");
  let foto = param.feature.getProperty("foto");
  console.log(row);
  console.log(foto);
  let img =
    foto != null
      ? `<img src="${jalandir}${no_jalan}/${row}/${foto}" width="300px" >`
      : "";
  console.log(img);
  return [
    /*html*/ `
    <tr>
        <td>Foto</td>
        <td>${img}</td>
    </tr>
    `,
  ];
};

let jembatanInfo = (param) => {
  let no_point = param.feature.getProperty("no_point");
  let nama_point = param.feature.getProperty("nama_point");
  let foto = param.feature.getProperty("foto");

  return [
    /*html*/ `
      <tr>
          <td>No Jembatan</td>
          <td>${no_point}</td>
      </tr>
      `,
    /*html*/ `
      <tr>
          <td>Nama ${nama_point}</td>
          <td>${nama_point}</td>
      </tr>
      `,
  ];
};

let positionInfo = () => {
  return [
    /*html*/ `
    <tr>
        <td>Lokasi Anda</td>
    </tr>
    `,
  ];
};

let CompleteLines;
let PerkerasanLines;
let KondisiLines;
let SegmentPoints;
let AwalPoints;
let AkhirPoints;
let JembatanPoints;

let DataJalan;
let JalanLines;
let loadDataJalan = (no_jalan) => {
  map_center = default_center;
  clearJalan();
  DataJalan = getAJAX(`${base_url}/Gis/index/datajalan/${no_jalan}`);
  if (DataJalan.length > 0) {
    DataJalan = JSON.parse(DataJalan);
    loadJalan();
    if (no_jalan != "semua") setFeatureCenter();
    return true;
  }
  return false;
};

let clearDataJalan = () => {};

let loadJalan = () => {
  JalanLines = loadData(DataJalan.jalan, "lines", "jalan");
};

let clearJalan = () => {
  if (JalanLines !== undefined) {
    JalanLines.setMap(null);

    clearComplete();
    clearPerkerasan();
    clearKondisi();
    clearSegment();
    clearAwal();
    clearAkhir();
    clearJembatan();
  }
};

let loadComplete = () => {
  CompleteLines = loadData(DataJalan.complete, "lines", "jalan");
};

let clearComplete = () => {
  if (CompleteLines !== undefined) {
    CompleteLines.setMap(null);
  }
};

let loadPerkerasan = () => {
  PerkerasanLines = loadData(DataJalan.perkerasan, "lines", "jalan");
};

let clearPerkerasan = () => {
  if (PerkerasanLines !== undefined) {
    PerkerasanLines.setMap(null);
  }
};

let loadKondisi = () => {
  KondisiLines = loadData(DataJalan.kondisi, "lines", "jalan");
};

let clearKondisi = () => {
  if (KondisiLines !== undefined) {
    KondisiLines.setMap(null);
  }
};

let loadSegment = () => {
  let icon = {
    url: `${server_base}/assets/img/circle.png`,
    scaledSize: new google.maps.Size(10, 10),
    anchor: new google.maps.Point(5, 5),
  };

  SegmentPoints = loadData(DataJalan.segment, "points", "segment", icon);
};

let clearSegment = () => {
  if (SegmentPoints !== undefined) {
    SegmentPoints.setMap(null);
  }
};

let loadAwal = () => {
  let icon = {
    url: `${server_base}/assets/img/triangle.png`,
    scaledSize: new google.maps.Size(10, 10),
    anchor: new google.maps.Point(5, 5),
  };

  AwalPoints = loadData(DataJalan.awal, "points", "awal", icon);
};

let clearAwal = () => {
  if (AwalPoints !== undefined) {
    AwalPoints.setMap(null);
  }
};

let loadAkhir = () => {
  let icon = {
    url: `${server_base}/assets/img/rhombus.png`,
    scaledSize: new google.maps.Size(10, 10),
    anchor: new google.maps.Point(5, 5),
  };

  AkhirPoints = loadData(DataJalan.akhir, "points", "akhir", icon);
};

let clearAkhir = () => {
  if (AkhirPoints !== undefined) {
    AkhirPoints.setMap(null);
  }
};

let loadJembatan = () => {
  let icon = {
    url: `${server_base}/assets/img/bridge.png`,
    scaledSize: new google.maps.Size(10, 10),
    anchor: new google.maps.Point(5, 5),
  };

  JembatanPoints = loadData(DataJalan.jembatan, "points", "jembatan", icon);
};

let clearJembatan = () => {
  if (JembatanPoints !== undefined) {
    JembatanPoints.setMap(null);
  }
};

let BatasLines;
let loadBatas = () => {
  map_data = `${server_base}/data/Batas.json?t=${cur_time}`;
  BatasLines = loadData(map_data, "border", "batas");
};

let setFeatureCenter = () => {
  coordinates = DataJalan.jalan.geometry.coordinates;

  // put all latitudes and longitudes in arrays
  let long = [];
  let lat = [];
  coordinates.forEach((i) => {
    long.push(i[0]);
    lat.push(i[1]);
  });

  // sort the arrays low to high
  lat.sort();
  long.sort();

  // get the min and max of each
  const lowX = lat[0];
  const highX = lat[lat.length - 1];
  const lowy = long[0];
  const highy = long[lat.length - 1];

  // center of the polygon is the starting point plus the midpoint
  const centerX = lowX + (highX - lowX) / 2;
  const centerY = lowy + (highy - lowy) / 2;

  map_center = { lat: centerX, lng: centerY };
  map.setCenter(map_center);
};

let PositionPoint;
let loadPosition = () => {
  let icon = {
    url: "http://maps.google.com/mapfiles/kml/paddle/red-circle.png",
    scaledSize: new google.maps.Size(20, 20),
    // anchor: new google.maps.Point(10, 10),
  };

  PositionPoint = loadData(DataJalan.position, "points", "position", icon);

  const [centerY, centerX] = DataJalan.position.geometry.coordinates;

  map_center = { lat: centerX, lng: centerY };
  map.setCenter(map_center);
};

let clearPosition = () => {
  if (PositionPoint !== undefined) {
    PositionPoint.setMap(null);
  }
};

let getOriginDestination = () => {
  return [
    DataJalan.position.geometry.coordinates,
    DataJalan.awal.features[0].geometry.coordinates,
  ];
};

let directionsService = new google.maps.DirectionsService();
let directionsRenderer = new google.maps.DirectionsRenderer({
  suppressMarkers: true,
});

let calcRoute = () => {
  const [start, end] = getOriginDestination();

  let request = {
    origin: new google.maps.LatLng(start[1], start[0]),
    destination: new google.maps.LatLng(end[1], end[0]),
    travelMode: "DRIVING",
  };

  directionsRenderer.setMap(map);

  directionsService.route(request, (result, status) => {
    if (status == "OK") {
      directionsRenderer.setDirections(result);
    }
  });
};

let clearRoute = () => {
  directionsRenderer.setMap(null);
};
