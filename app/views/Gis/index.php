<div class="w-100">
  <div id="mySidepanel" class="sidepanel border">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs nav-fill clr-2018-coconut-milk border-bottom">
      <li class="nav-item order-2">
        <a class="nav-link" href="#legend">Legenda</a>
      </li>
      <li class="nav-item order-1">
        <a class="nav-link active" href="#cari">Cari</a>
      </li>
      <li class="nav-item order-4">
        <a href="javasript:void(0)" class="close btn-sidebar-close p-1" aria-label="Close">
          <i class="material-icons">close</i>
        </a>
      </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
      <div id="legend" class="tab-pane fade py-3 px-3">
        <div class="d-flex flex-column">
          <div class="pr-3 legend-title">Perkerasan:</div>
          <div class="pr-3">
            <svg height="16" width="20">
              <polygon points="0,4 0,10 18,10 18,4" style="fill:#5c5c5b;"></polygon>
            </svg>
            Jalan Beton
          </div>
          <div class="pr-3">
            <svg height="16" width="20">
              <polygon points="0,4 0,10 18,10 18,4" style="fill:#373538;"></polygon>
            </svg>
            Jalan Aspal
          </div>
          <div class="pr-3">
            <svg height="16" width="20">
              <polygon points="0,4 0,10 18,10 18,4" style="fill:#745d46;"></polygon>
            </svg>
            Jalan Kerikil
          </div>
          <div class="pr-3">
            <svg height="16" width="20">
              <polygon points="0,4 0,10 18,10 18,4" style="fill:#ac7e54;"></polygon>
            </svg>
            Jalan Tanah
          </div>

          <div class="pr-3 legend-title">Kondisi:</div>
          <div class="pr-3">
            <svg height="16" width="20">
              <polygon points="0,4 0,10 18,10 18,4" style="fill:#28a745;"></polygon>
            </svg>
            Baik
          </div>
          <div class="pr-3">
            <svg height="16" width="20">
              <polygon points="0,4 0,10 18,10 18,4" style="fill:#17a2b8;"></polygon>
            </svg>
            Sedang
          </div>
          <div class="pr-3">
            <svg height="16" width="20">
              <polygon points="0,4 0,10 18,10 18,4" style="fill:#ffc107;"></polygon>
            </svg>
            Rusak Ringan
          </div>
          <div class="pr-3">
            <svg height="16" width="20">
              <polygon points="0,4 0,10 18,10 18,4" style="fill:#dc3545;"></polygon>
            </svg>
            Rusak Berat
          </div>

          <div class="pr-3 legend-title">Marker:</div>
          <div class="pr-3">
            <img src="{$smarty.const.SERVER_BASE}/assets/img/triangle.png" alt="">
            Titik Awal Ruas Jalan
          </div>
          <div class="pr-3">
            <img src="{$smarty.const.SERVER_BASE}/assets/img/circle.png" alt="">
            Segmentasi
          </div>
          <div class="pr-3">
            <img src="{$smarty.const.SERVER_BASE}/assets/img/rhombus.png" alt="">
            Titik Akhir Ruas Jalan
          </div>
          <div class="pr-3">
            <img src="{$smarty.const.SERVER_BASE}/assets/img/bridge.png" width="12px" alt="">
            Jembatan
          </div>
        </div>
      </div>
      <div id="cari" class="tab-pane pt-3 active">
        {$data.searchform}
        <div class="container tracking-action-container mb-2">
          <button type="button" class="btn btn-light" id="yourLocation">
            <div><i class='fas fa-map-marker-alt text-danger'></i>&nbsp;Tempat Anda</div>
          </button>
          <button type="button" class="btn btn-light" id="routeLocation">
            <div><i class='fas fa-directions text-info'></i>&nbsp;Rute</div>
          </button>
          <button type="button" class="btn btn-light" id="trackingLocation">
            <div><i class='fas fa-location-arrow text-success'></i>&nbsp;Tracking</div>
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="map-wrapper order-md-1 order-sm-2 order-2">
    <!--Star Map Area-->
    <div class="map-bg border">
      <div class="kotakpeta">
        <div id="map_canvas"></div>
      </div>
    </div>
    <!--Form Map Area-->
  </div>
</div>

{literal}
<script>
  window.onload = function() {
    initMap();
  };
</script>
{/literal}