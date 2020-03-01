<div class="mb-3">
    <div id="mySidenav" class="sidenav">
        <div class="card">
            <div class="card-header bg-secondary text-light py-2">
                <i class="fas fa-search"></i>&nbsp;Cari<button class="btn-sidebar-close close">&times;</button>
            </div>
            <div class="card-body p-0">
                {$data.searchform}
                <div class=" search-btn d-flex justify-content-center mb-3">
                    {foreach from=$data.searchbtn key=k item=v}
                    {$v}
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-9">
            <!--Star Map Area-->
            <div class="map-bg">
                <div class="kotakpeta">
                    <div id="map_canvas"></div>
                </div>
            </div>
            <!--Form Map Area-->
        </div>
        <div class="col-sm-3">
            <div class="border p-2">
                <div>
                    <strong>Legend:</strong>
                </div>
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
        </div>
    </div>
</div>

{literal}
<script>
    window.onload = function() {
        initMap();
    };
</script>
{/literal}