<?php

// NAVIGATION
define("UT_NAV",                DB_LOC.".navigation");                        // navigation
define("UT_NAV_ITE",            DB_LOC.".navigation_items");                  // navigation items, presentation order


// ITEMS
define("UT_ITE",                DB_REG.".items");                             // items
define("UT_ITE_TAG",            DB_REG.".item_tags");                         // item tags
define("UT_TAG",                DB_REG.".tags");                              // tags
define("UT_TAG_BLI",            DB_REG.".tags_blist");                        // tags, b-list (unapproved admin tags)
define("UT_TAG_CLI",            DB_REG.".tags_clist");                        // tags, c-list (unapproved www tags)

define("UT_ITE_DES",            DB_REG.".item_descriptions");                 // item descriptions
define("UT_ITE_COM",            DB_REG.".item_comments");                     // item comments

define("UT_PRI",                DB_REG.".price_groups");                      // price groups
define("UT_ITE_PRI",            DB_REG.".item_prices");                       // item prices


// ITEMTYPES
define("UT_ITT_BLO",            DB_REG.".itemtype_blog");                     // itemtype blog
define("UT_ITT_LOG",            DB_REG.".itemtype_log");                      // itemtype log
define("UT_ITT_PHO",            DB_REG.".itemtype_photo");                    // itemtype photo
define("UT_ITT_TXT",            DB_REG.".itemtype_text");                     // itemtype text
define("UT_ITT_HTM",            DB_REG.".itemtype_html");                     // itemtype text
define("UT_ITT_NEW",            DB_REG.".itemtype_news");                     // itemtype news
define("UT_ITT_PRO",            DB_REG.".itemtype_product");                  // itemtype product
define("UT_ITT_BUN",            DB_REG.".itemtype_bundle");                   // itemtype bundle
define("UT_ITT_BUN_ITE",        DB_REG.".itemtype_bundle_items");             // itemtype bundle items

?>