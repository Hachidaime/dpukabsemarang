<div class="w-100">
    {*
    <div id="mySidepanel" class="sidepanel border">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-fill clr-2018-coconut-milk border-bottom">
            <li class="nav-item order-2">
                <a class="nav-link" href="#legend">Legend</a>
            </li>
            <li class="nav-item order-1">
                <a class="nav-link active" href="#cari">Cari</a>
            </li>
            <li class="nav-item order-3">
                <a class="nav-link" href="#info">Info</a>
            </li>
            <li class="nav-item order-4">
                <a href="javasript:void(0)" class="close btn-sidebar-close p-1" aria-label="Close">
                    <i class="material-icons">close</i>
                </a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div id="legend" class="tab-pane fade pt-3 px-3">
                <div class="d-flex flex-column">

                    <div class="pr-3 py-1">
                        <svg height="18" width="20">
                            <polygon points="0,8 0,14 18,14 18,8" style="fill:#00b050;"></polygon>
                        </svg>
                        Baik
                    </div>
                    <div class="pr-3 py-1">
                        <svg height="18" width="20">
                            <polygon points="0,8 0,14 18,14 18,8" style="fill:#0070c0;"></polygon>
                        </svg>
                        Sedang
                    </div>
                    <div class="pr-3 py-1">
                        <svg height="18" width="20">
                            <polygon points="0,8 0,14 18,14 18,8" style="fill:#ffc000;"></polygon>
                        </svg>
                        Rusak Ringan
                    </div>
                    <div class="pr-3 py-1">
                        <svg height="18" width="20">
                            <polygon points="0,8 0,14 18,14 18,8" style="fill:#e36c09;"></polygon>
                        </svg>
                        Rusak Berat
                    </div>
                    <div class="pr-3 py-1">
                        <svg height="18" width="20">
                            <polygon points="9,2 2,16 15,16" style="fill:#4681b4;stroke:#325c81;stroke-width:3"></polygon>
                        </svg>
                        Titik Awal Ruas Jalan
                    </div>
                    <div class="pr-3 py-1">
                        <svg height="18" width="20">
                            <circle cx="9" cy="9" r="7" style="fill:#4681b4;stroke:#325c81;stroke-width:3"></circle>
                        </svg>
                        Segmentasi
                    </div>
                    <div class="pr-3 py-1">
                        <svg height="18" width="20">
                            <polygon points="9,2 2,9 9,16 16,9" style="fill:#4681b4;stroke:#325c81;stroke-width:3"></polygon>
                        </svg>
                        Titik Awal Ruas Jalan
                    </div>

                </div>
            </div>
            <div id="cari" class="tab-pane pt-3 active">
                {$data.searchform}
                <div class=" search-btn d-flex justify-content-center mb-3">
                    {foreach from=$data.searchbtn key=k item=v}
                    {$v}
                    {/foreach}
                </div>
            </div>
            <div id="info" class="tab-pane fade pt-3 px-3">
                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.</p>
            </div>
        </div>
    </div>
    *}
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
        initMap2();
    };
</script>
{/literal}