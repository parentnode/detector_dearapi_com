<?php

define("DB_LOC",                SITE_DB);
define("DB_REG",                SITE_DB);
define("DB_GLO",                SITE_DB);
define("DB_FRA",                "framework");


// ACCESS
define("UT_ACC_LEV",            DB_GLO.".users_access_levels");               // access levels
define("UT_ACC_POI",            DB_GLO.".users_access_points");               // access points
define("UT_ACC_LEV_POI",        DB_GLO.".users_access_level_points");         // access specifications

// USERS
define("UT_USE",                DB_GLO.".users");                             // users

// MENU
define("UT_MEN",                DB_GLO.".users_menu");                        // menu layout



// BASICS - COUNTRIES
define("UT_BAS_COU_CUR",        DB_GLO.".basics_currencies");                  // currencies
define("UT_BAS_COU",            DB_GLO.".basics_countries");                   // country
define("UT_BAS_COU_VAT",        DB_GLO.".basics_vat_rates");                   // VAT-rate
define("UT_BAS_LAN",            DB_GLO.".basics_languages");                   // languages


// BASICS - CONTENTTYPES
define("UT_BAS_CON",            DB_GLO.".basics_contenttypes");                // contenttypes
define("UT_BAS_BRA",            DB_GLO.".basics_brands");                             // device brands


// DEVICES
define("UT_DEV",                DB_GLO.".devices");                            // device
define("UT_DEV_CON",            DB_GLO.".device_contenttypes");                // device contenttypes
define("UT_DEV_USE",            DB_GLO.".device_useragents");                  // useragents
define("UT_DEV_UNI",            DB_GLO.".devices_unidentified");               // unidentified devices


$regional_databases = array();
$local_databases = array();

?>