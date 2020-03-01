<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href={$smarty.const.SERVER_BASE}/assets/img/smgkab.png> <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Title -->
    <title>{$smarty.session.title_bar}</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="{$smarty.const.SERVER_BASE}/assets/css/bootstrap.min.css?t={$smarty.now|time}">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.15.5/dist/bootstrap-table.min.css">

    <link rel="stylesheet" href="{$smarty.const.SERVER_BASE}/assets/css/bootstrap.colorpickersliders.css?t={$smarty.now|time}">

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.12/dist/css/bootstrap-select.min.css">

    <!-- Font Awesome-->
    {*<script src="https://kit.fontawesome.com/d55aaa9ea7.js" crossorigin="anonymous"></script>*}
    <link rel="stylesheet" href="{$smarty.const.SERVER_BASE}/assets/css/all.css">


    <script src="http://maps.google.com/maps/api/js?v=3&libraries=geometry&key={$smarty.const.GMAPS_API_KEY}" type="text/javascript"></script>

    <link rel="stylesheet" href="{$smarty.const.SERVER_BASE}/assets/libs/jquery-ui/jquery-ui.min.css">
    <link rel="stylesheet" href="{$smarty.const.SERVER_BASE}/assets/libs/jquery-ui/jquery-ui.structure.min.css">
    <link rel="stylesheet" href="{$smarty.const.SERVER_BASE}/assets/libs/jquery-ui/jquery-ui.theme.min.css">

    <link rel="stylesheet" href="{$smarty.const.SERVER_BASE}/assets/css/tagsinput.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{$smarty.const.SERVER_BASE}/assets/css/bg-color.css?t={$smarty.now|time}">
    <link rel="stylesheet" href="{$smarty.const.SERVER_BASE}/assets/css/text-color.css?t={$smarty.now|time}">
    <link rel="stylesheet" href="{$smarty.const.SERVER_BASE}/assets/css/border-color.css?t={$smarty.now|time}">
    <!--
-->
    <link rel="stylesheet" href="{$smarty.const.SERVER_BASE}/assets/css/custom.css?t={$smarty.now|time}">

</head>

<body>
    <div class="main-container container p-0 min-vh-100 border bg-light">
        <!-- <div class="header sticky-top"> -->
        {$header}
        <!-- </div> -->

        <div class="content p-3">
            {$content}
        </div>
    </div>

    <div class="footer">
        <nav class="container navbar justify-content-center bg-secondary text-white navbar-dark">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <i class="far fa-copyright"></i>&nbsp;{$smarty.now|date_format:'%Y'}
                </li>
            </ul>
        </nav>
    </div>
    <div class="snackbar"></div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="{$smarty.const.SERVER_BASE}/assets/libs/jquery-ui/jquery-ui.min.js"></script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/tinycolor/0.11.1/tinycolor.min.js"></script>

    <!-- Proper -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>

    <!-- Bootstrap -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.15.5/dist/bootstrap-table.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.15.5/dist/extensions/export/bootstrap-table-export.min.js"></script>
    <script src="https://unpkg.com/tableexport.jquery.plugin/libs/jsPDF/jspdf.min.js"></script>
    <script src="https://unpkg.com/tableexport.jquery.plugin/libs/jsPDF-AutoTable/jspdf.plugin.autotable.js"></script>

    <script src="{$smarty.const.SERVER_BASE}/assets/js/bootstrap.colorpickersliders.js?t={$smarty.now|time}"></script>

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.12/dist/js/bootstrap-select.min.js"></script>

    <script src="{$smarty.const.SERVER_BASE}/assets/js/tagsinput.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.2/dist/sweetalert2.all.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>

    <script type="text/javascript" src="{$smarty.const.SERVER_BASE}/assets/js/v3_epoly.js"></script>
    <script src='https://npmcdn.com/@turf/turf/turf.min.js'></script>

    <!-- Custom JS -->
    <script>
        const base_url = '{$smarty.const.BASE_URL}';
        const server_base = '{$smarty.const.SERVER_BASE}';
        const controller = '{$data.controller}';
        const method = '{$data.method}';
        const DEFAULT_LATITUDE = Number(`{$smarty.const.DEFAULT_LATITUDE}`);
        const DEFAULT_LONGITUDE = Number(`{$smarty.const.DEFAULT_LONGITUDE}`);
    </script>
    <script src="{$smarty.const.SERVER_BASE}/assets/js/custom-functions.js?t={$smarty.now|time}"></script>
    <script src="{$smarty.const.SERVER_BASE}/assets/js/custom-bootstrap-table.js?t={$smarty.now|time}"></script>
    <script src="{$smarty.const.SERVER_BASE}/assets/js/map.js?t={$smarty.now|time}"></script>
    <script src="{$smarty.const.SERVER_BASE}/assets/js/custom.js?t={$smarty.now|time}"></script>
</body>

</html>