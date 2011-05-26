<?php
# PHPKIT Web Content Management System
# --------------------------------------------
# Copyright (c) 2002-2007 Gersöne & Schott GbR
#
# This file / the PHPKIT-software is no freeware!
# For further informations please vistit our website
# or contact us via email:
#
# Diese Datei / die PHPKIT-Software ist keine Freeware!
# Für weitere Information besuchen Sie bitte unsere 
# Webseite oder kontaktieren uns per E-Mail:
#
# Website : http://www.phpkit.de
# Mail    : info@phpkit.de
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATIONS
#
# SIE SIND NICHT BERECHTIGT, UNRECHTMÄSSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN


pkLoadFunc('system');

return array(
'dir_does_not_exists'=>'Verzeichnis existiert nicht',
'fail'=>'fehlen',
'note_optional'=>' (optional)',
'php_fileuploads'=>'PHP-Upload von Dateien',
'phpversion'=>'PHP-Version',
'php_extension_gdlib'=>'PHP-Erweiterung GdLib',
'safemode'=>'PHP Safe-Mode',
'set'=>'gesetzt',
'system_fileupload_required'=>pkGetLang(pkSYSTEMREQUIREDFILEUPLOADS ? 'enabled':'disabled'),
'system_phpversion_required'=>pkSYSTEMREQUIREDPHPVERSION.' oder h&ouml;her',
'system_safemode_required'=>pkGetLang(pkSYSTEMREQUIREDSAFEMODE ? 'enabled':'disabled'),
'write_permissions_dirs'=>'Verzeichnischreibrechte f&uuml;r &quot;%s&quot;',
);
?>