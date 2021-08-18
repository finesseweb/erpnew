<?php

/* How to make new entries here for non-packaged languages:

-- 'code' should match the name of the directory for the language under \lang
.-- 'name' is the name that will be displayed in the language selection list (in Users and Display Setup)
-- 'rtl' only needs to be set for right-to-left languages like Arabic and Hebrew
-- 'encoding' used in translation file
-- 'version' always set to '' for manually installed languages.
-- 'path' installation path related to FA root (e.g. 'lang/en_US').
*/


$installed_languages = array (
  0 => 
  array (
    'code' => 'C',
    'name' => 'English',
    'encoding' => 'iso-8859-1',
  ),
  1 => 
  array (
    'name' => 'English (IN)',
    'package' => 'en_IN',
    'code' => 'en_IN',
    'encoding' => 'iso-8859-1',
    'version' => '2.4.1-3',
    'path' => 'lang/en_IN',
  ),
);

$dflt_lang = 'en_IN';
