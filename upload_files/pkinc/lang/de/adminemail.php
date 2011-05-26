<?php
# PHPKIT WCMS | Web Content Management System
#
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATION
#
# SIE SIND NICHT BERECHTIGT, UNRECHTM�SSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN
#
# This file / the PHPKIT software is no freeware! For further 
# information please visit our website or contact us via email:
#
# Diese Datei / die PHPKIT Software ist keine Freeware! F�r weitere
# Informationen besuchen Sie bitte unsere Website oder kontaktieren uns per E-Mail:
#
# email     : info@phpkit.com
# website   : http://www.phpkit.com
# licence   : http://www.phpkit.com/licence
# copyright : Copyright (c) 2002-2009 mxbyte gbr | http://www.mxbyte.com


return array(
#CONTENT SUBMITED
'contentsubmited_mail_title'		 			=> 'Eingesendeter Beitrag auf %s',
'contentsubmited_mail_body_accepted' 			=> 'Hallo %s,

Der von Ihnen eingsendete Beitrag wurde in unsere Datenbank aufgenommen und wird in K�rze in auf %s zu finden sein.

Wir bedanken uns recht herzlich f�r Ihre Einsendung.

%s


Mit freundlichen Gr��en

Das %s Website-Team
%s',#1.author, 2.sitename,3.link,4.siteurl

'contentsubmited_mail_body_declined'			=> 'Hallo %s,

Der von Ihnen eingesendete Beitrag wurde gepr�ft, konnte aber nicht aufgenommen werden.

Trotzdem bedanken wir uns f�r den Vorschlag und die damit verbundene M�he.


Mit freundlichen Gr��en

Das %s Website-Team
%s',#1.author


#USER ACTIVATE
'user_activate_mail_title'						=> 'Registrierung f�r die %s Website',
'user_activate_mail_text'						=> 'Willkommen %s,


Ihr Benutzerkonto f�r die %s Website wurde freigeschaltet.

Benutzen Sie diesen Link um sich direkt anzumleden: 

%s


Beachten Sie bitte, das Ihr Browser die Annahme von 
Cookies akzeptieren muss, damit Sie sich einloggen k�nnen!




Mit freundlichen Gr��en


Das %s Team
%s',#1.username 2.sitename 3.link,4.sitename,5.siteurl


#USER EDIT
'user_edit_mail_title'							=> 'Benutzerkonto %s auf %s bearbeitet',
'user_edit_mail_text'							=> 'Hallo %s,

Ihr Benutzeraccount auf %s wurde soeben durch einen Administrator bearbeitet.
%s
Benutzen Sie f�r die n�chste Anmeldung bitte den folgenden Link:

%s


Mit freundlichen Gr��en


Das %s Team
%s',#1.username,2.sitename,3.note,4.link,5.sitename6.siteurl
'user_edit_mail_textadd'						=> '
Als Begr�ndung f�r die Bearbeitung wurde folgendes angegeben:

%s


',

#USER EDIT (DELETE)
'user_delete_mail_title'						=> 'Benutzerkonto %s auf %s gel�scht',#1.username,2.sitename
'user_delete_mail_text'							=> 'Hallo,


das Benutzerkonto "%s" auf der %s Website wurde gel�scht. 

%s


Mit freundlichen Gr��en


Das %s Team
%s',#1.username,2.sitename,3.notifytext,4.sitename,5.siteurl

'user_delete_mail_text_reason'					=> 'F�r die L�schung des Benutzerkontos wurde die folgende Begr�ndung angegeben:

%s',
'user_delete_mail_text_noreason'				=> 'Eine Begr�ndung f�r die L�schung des Benutzerkontos wurde nicht angegeben.',


#INFOMAIL 
'infomail_body_txt'								=> '%s


%s



%s - %s',#1.title,2.text,3.sitename,4.siteurl
'infomail_body_html'							=> '<html>

<head>
<title>%s</title>
</head>

<body>
<h2>%s</h2>
%s<br />
<br />
<br />
<strong>%s</strong><br />
<a href="%s">%s</a>
</body>

</html>',#1.title,2.title,3.text,4.sitename,5.siteurls,6.siteurl short
);
?>