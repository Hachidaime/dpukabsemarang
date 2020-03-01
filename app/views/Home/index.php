<div class="row">
    <div class="col-md-4">
        <div>
            <p class="text-justify">
                Sistem Jaringan Jalan Kabupaten Semarang, sebagai penguatan database dan survey kondisi jalan dalam bentuk visualisasi GIS (Geographic Information System) dalam membantu Dinas Pekerjaan Umum Kabupaten Semarang, Bidang Bina Marga sebagai Tupoksi Bidang Pembangunan, Pemeliharaan Jalan dan Jembatan.
            </p>
        </div>
    </div>
    <div class="col-md-8">
        <img src="https://dummyimage.com/1024x600/A9A9A9/fff.png&text=Lorem+ipsum" class="img-fluid mb-3" >
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        {foreach from=$smarty.const.SITE_INFO key=k item=v}
        {if $k eq 'Alamat'}
        <p class="text-justify">
            <strong>{$k}</strong>: {$v}
        </p>
        {else}
        <div class="row">
            <div class="col-md-3 pr-0"><strong>{$k}<span class="float-lg-right float-md-right float-sm-none">:</span></strong></div>
            <div class="col-md-9">{$v}</div>
        </div>
        {/if}
        {/foreach}
    </div>
    <div class="col-md-8">
        <div id="demo" class="carousel slide" data-ride="carousel">

            <!-- Indicators 
            <ul class="carousel-indicators">
            <li data-target="#demo" data-slide-to="0" class="active"></li>
            <li data-target="#demo" data-slide-to="1"></li>
            <li data-target="#demo" data-slide-to="2"></li>
            <li data-target="#demo" data-slide-to="3"></li>
            </ul>
            -->

            <!-- The slideshow -->
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="https://dummyimage.com/800x200/000/fff.png&text=Lorem+ipsum" alt="Los Angeles" class="img-fluid">
                </div>
                <div class="carousel-item">
                    <img src="https://dummyimage.com/800x200/f00/fff.png&text=Lorem+ipsum" alt="Chicago" class="img-fluid">
                </div>
                <div class="carousel-item">
                    <img src="https://dummyimage.com/800x200/0f0/fff.png&text=Lorem+ipsum" alt="New York" class="img-fluid">
                </div>
                <div class="carousel-item">
                    <img src="https://dummyimage.com/800x200/00f/fff.png&text=Lorem+ipsum" alt="New York" class="img-fluid">
                </div>
            </div>

            <!-- Left and right controls -->
            <a class="carousel-control-prev" href="#demo" data-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </a>
            <a class="carousel-control-next" href="#demo" data-slide="next">
                <span class="carousel-control-next-icon"></span>
            </a>
        </div>
        <!-- <img src="https://dummyimage.com/1024x200/000/fff.png&text=Lorem+ipsum" class="img-fluid" > -->
    </div>
</div>