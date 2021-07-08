<?php
/**
* This file contains definitions
*
* @package Config
*/
header("Content-type: text/html; charset=UTF-8");
error_reporting(E_ALL);

define("VERSION", "0.7.9.2");

define("SITE_UID", "DV3");
define("SITE_NAME", "detector-v3.dearapi.com");
define("SITE_URL", (isset($_SERVER["HTTPS"]) ? "https" : "http")."://".$_SERVER["SERVER_NAME"]);
define("SITE_EMAIL", "info@parentnode.dk");

define("DEFAULT_PAGE_DESCRIPTION", "");
define("DEFAULT_PAGE_IMAGE", "/img/logo.png");

define("DEFAULT_LANGUAGE_ISO", "EN");
define("DEFAULT_COUNTRY_ISO", "DK");
define("DEFAULT_CURRENCY_ISO", "DKK");

define("SITE_LOGIN_URL", "/login");

define("SITE_SIGNUP", false);
define("SITE_SIGNUP_URL", "/signup");

define("SITE_ITEMS", true);

define("SITE_SHOP", false);
define("SHOP_ORDER_NOTIFIES", "martin@think.dk");

define("SITE_SUBSCRIPTIONS", false);

define("SITE_MEMBERS", false);

define("SITE_COLLECT_NOTIFICATIONS", 500);

