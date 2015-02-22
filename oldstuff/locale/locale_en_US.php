<?php

// include UK messages, and then just change the ones we don't like

// NOTE: there is a problem with this method due to the way the language
//       file is grabbed for plugins, so this method only works for
//       English, unless you want to create a locale file for each plugin

include(PLOG_CLASS_PATH . "locale/locale_en_UK.php" );  

$messages['locale_description'] = 'English/American locale file for LifeType';

// As dumb as it is, Americans like the month/day/year format
$messages['date_format'] = '%m/%d/%Y %H:%M';

?>