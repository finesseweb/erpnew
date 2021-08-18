<?php

/* List of installed additional extensions. If extensions are added to the list manually
	make sure they have unique and so far never used extension_ids as a keys,
	and $next_extension_id is also updated. More about format of this file yo will find in 
	FA extension system documentation.
*/

$next_extension_id = 8; // unique id for next installed extension

$installed_extensions = array (
  0 => 
  array (
    'name' => 'English Indian COA - New.',
    'package' => 'chart_en_IN-general',
    'version' => '2.4.1-4',
    'type' => 'chart',
    'active' => false,
    'path' => 'sql',
    'sql' => 'en_IN-new.sql',
  ),
  1 => 
  array (
    'name' => 'Theme Dynamic',
    'package' => 'dynamic',
    'version' => '2.4.0-3',
    'type' => 'theme',
    'active' => false,
    'path' => 'themes/dynamic',
  ),
  2 => 
  array (
    'name' => 'Theme Exclusive',
    'package' => 'exclusive',
    'version' => '2.4.0-1',
    'type' => 'theme',
    'active' => false,
    'path' => 'themes/exclusive',
  ),
  3 => 
  array (
    'name' => 'Theme Exclusive for Dashboard',
    'package' => 'exclusive_db',
    'version' => '2.4.0-1',
    'type' => 'theme',
    'active' => false,
    'path' => 'themes/exclusive_db',
  ),
  4 => 
  array (
    'name' => 'FrontHrm',
    'package' => 'fronthrm',
    'version' => '2.4.0-1',
    'type' => 'extension',
    'active' => false,
    'path' => 'modules/FrontHrm',
  ),
  5 => 
  array (
    'name' => 'Inventory History',
    'package' => 'rep_inventory_history',
    'version' => '2.4.0-1',
    'type' => 'extension',
    'active' => false,
    'path' => 'modules/rep_inventory_history',
  ),
  6 => 
  array (
    'package' => 'ExtendedHRM',
    'name' => 'Payroll',
    'version' => '2.4',
    'available' => '',
    'type' => 'extension',
    'path' => 'modules/ExtendedHRM',
    'active' => false,
  ),
  7 => 
  array (
    'package' => 'FrontHrm',
    'name' => 'FrontHrm',
    'version' => '-',
    'available' => '',
    'type' => 'extension',
    'path' => 'modules/FrontHrm',
    'active' => false,
  ),
);
