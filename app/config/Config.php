<?php

// * Default Class, Action, & Menu
define('DEFAULT_CONTROLLER', "Home");
define('DEFAULT_METHOD', "index");
define('DEFAULT_MENU_ID', 2); // ? Default menu_id : 1 = Admin, 2 = Website, 3 = Properties

// ? Date and time format in Smarty Template Engine
define('SMARTY_DATETIME_FORMAT', "%d/%m/%Y %I:%M%p"); // ? d/m/Y H:ia
define('SMARTY_DATE_FORMAT', "%d/%m/%Y"); // ? d/m/Y
define('SMARTY_TIME_FORMAT', "%I:%M%p"); // ? H:ia

// * Authentification Token
define('TOKEN_LENGTH', 8); // ? Authentification token's lenght
define('TOKEN_ACTIVE_PERIOD', 1800); // ? Token's active period, 30 minutes (1800 = 30 * 60 second)

// ? Bootstrap color
define('BS_COLOR', ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark']);

// ? Status
DEFINE('DISPLAY_STATUS', [1 => "Shown", 2 => "Hidden"]);
