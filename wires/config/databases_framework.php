<?php

// ACCESS
define("UT_ACC_LEV",            DB_REG.".users_access_levels");               // access levels
define("UT_ACC_POI",            DB_REG.".users_access_points");               // access points
define("UT_ACC_LEV_POI",        DB_REG.".users_access_level_points");         // access specifications

// MENU
define("UT_MEN",                DB_REG.".users_menu");                        // menu layout

// USERS
define("UT_USE",                DB_REG.".users");                             // users
//define("UT_USE_ADD",            DB_REG.".users_user_addresses");              // users


// BASICS - COUNTRIES
//define("UT_COU_CUR",            DB_REG.".basics_currencies");                  // currencies
define("UT_BAS_COU",                DB_REG.".basics_countries");                   // country
//define("UT_COU_VAT",            DB_REG.".basics_vat_rates");                   // VAT-rate
define("UT_BAS_LAN",                DB_REG.".basics_languages");                   // languages


// BASICS - ITEMTYPES
define("UT_BAS_ITT",            DB_REG.".basics_itemtypes");                   // itemtypes

//define("UT_BAS_ITT_CON",        DB_REG.".basics_itemtype_contenttypes");       // itemtype contenttypes

// BASICS - CONTENTTYPES
define("UT_BAS_CON",            DB_GLO.".basics_contenttypes");                // contenttypes

define("UT_BAS_BRA",            DB_GLO.".basics_brands");                             // device brands

// FEEDBACK
define("UT_BAS_FEE",            DB_GLO.".basics_feedback");                           // feedback


//TRANSLATION HISTORY
//define("UT_TRA_HIS",            DB_FRA.".translation_history");         //translation history

$regional_databases = array();
$local_databases = array();

?>