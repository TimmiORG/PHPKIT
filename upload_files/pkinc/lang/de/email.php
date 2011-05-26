<?php
# PHPKIT WCMS | Web Content Management System
#
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATION
#
# SIE SIND NICHT BERECHTIGT, UNRECHTMÄSSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN
#
# This file / the PHPKIT software is no freeware! For further 
# information please visit our website or contact us via email:
#
# Diese Datei / die PHPKIT Software ist keine Freeware! Für weitere
# Informationen besuchen Sie bitte unsere Website oder kontaktieren uns per E-Mail:
#
# email     : info@phpkit.com
# website   : http://www.phpkit.com
# licence   : http://www.phpkit.com/licence
# copyright : Copyright (c) 2002-2009 mxbyte gbr | http://www.mxbyte.com


$dontanswer = '


#########################################################################################
Diese E-Mail wurde automatisch erzeugt, bitte antworten Sie daher NICHT auf diese E-Mail.
#########################################################################################
';


return array(
#COMMON
'subject'										=> 'Betreff',

#CONTACT FORM
'contact_text'									=> 'M&ouml;chten Sie uns Ihre Meinung mitteilen?<br />Haben Sie Kritik, Anregungen, Vorschl&auml;ge oder eine Idee, die Sie gerne einbringen m&ouml;chten?<br />Oder haben Sie eine Frage an uns?<br /><br />F&uuml;r all dies haben wir ein offenes Ohr! F&uuml;llen Sie einfach dieses Formular aus und wir antworten Ihnen schnellstm&ouml;glich!<br /><br />',
'copy_to_email'									=> 'Kopie an Ihre E-Mail-Adresse',

'send_recommendation'							=> 'Empfehlung versenden',


#
#plain/text elements
#
#COMMENT MAIL NOTIFY
'comment_mail_notify'							=> 'Auf %s hat %s einen neuen Kommentar erstellt.

Klicken Sie bitte  auf den folgenden Link um sich den Eintrag anzusehen: %s',#1.sitename 2. author 3.link

#CONTACT 
'contact_main_subject'							=> 'Feedback f&uuml;r %s: %s',
'contact_main_body_sender'						=> "Ihre Nachricht wurde an den Webmaster von %s gesendet. F&uuml;r weitere Fragen, k&ouml;nnen Sie auch die folgende E-Mail-Adresse verwenden: %s
\r\nSie schrieben die folgende Nachricht: 

%s", #1.site_name, 2.email 3.message

'contact_main_body_master'						=> 'Am %s hat %s die folgende Nachricht &uuml;ber das Kontaktformular auf %s gesendet:

%s', #1.time, 2.sender_name 3.site name 4. message


#FORUM NEWPOST NOTIFY
'newpost_notify_mail_title'						=> '%s - Neuer Forenbeitrag: %s',#1.sitename, 2.threadtitle
'newpost_notify_mail_body'						=> 'Auf %s hat %s einen neuen Forenbeitrag erstellt.

Klicken Sie bitte auf den folgenden Link um sich den Eintrag anzusehen:
%s
'.$dontanswer,#1.sitename, 2.author, 3.link


#FORUM NEWPOST SUBSCRIBER
'newpost_subscriber_mail_title'					=> '%s - Neuer Forenbeitrag: %s',#1.sitename, 2.threadtitle
'newpost_subscriber_mail_body'					=> 'Auf %s hat %s einen neuen Foreneintrag in dem von Ihnen beobachteten Thema $%s erstellt.

Klicken Sie auf den folgenden Link um sich den Bettrag jetzt anzusehen:
%s


Mit freundlichen Gr&uuml;&szlig;en

Das %s Team
%s
'.$dontanswer,#1.sitename,2.postautor,3.threadtitle,4.link,5.sitename,6.siteurl


#FORUMNOTIFY REPLY
'forumnotify_thread_reply_title'				=> '%s - Neuer Forenbeitrag im Thema: %s',
'forumnotify_thread_reply_text'					=> 'Guten Tag,

Soeben wurde auf Thema "%s" von %s, in unserem Forum geantwortet.
Klicken Sie bitte auf den folgenden Link um sich den Eintrag anzusehen:
%s


Sie haben diese E-Mail erhalten, weil Sie bei neuen Beitr&auml;gen benachrichtigt werden wollten.
Wenn Sie keine weiteren Benachrichtigungen mehr erhalten m&uuml;chten, so klicken Sie bitte hier:
%s
'.$dontanswer,


#FORUMNOTIFY REPLY
'forumnotify_thread_reply_text_mailnotify'		=> 'Guten Tag,

Soeben wurde auf das Thema "%s" von %s, in unserem Forum geantwortet.
Klicken Sie bitte auf den folgenden Link um sich den Eintrag anzusehen:
%s


Sie haben diese E-Mail erhalten, weil Sie bei neuen Beitr&auml;gen benachrichtigt werden wollten.
Wenn Sie keine weiteren Benachrichtigungen mehr erhalten m&ouml;chten, so wenden Sie sich bitte an
das Forenteam.

'.$dontanswer,


#FORUMNOTIFY REPLY
'forumnotify_thread_reply_text_pmnotify'		=> 'Guten Tag,

soeben wurde von %s ein neuer Forenbeitrag / ein neues Forenthema erstellt (Titel des Themas: %s).

Wenn Sie sich den Beitrag jetzt ansehen m&ouml;chten, [url=%s]klicken Sie bitte hier [/url].
',


#SUGGEST - preset/entries
'suggest_title_plain'							=> 'Interessante Seite auf %s gefunden',
'suggest_text'									=> 'Hallo%s,

ich habe eine Seite gefunden, die dich interessieren k&ouml;nnte:

%s',


#LOST PASSWORD
'lostpassword_mail_title'						=> 'Passwort vergessen bei %s',
'lostpassword_mail_text'						=> 'Hallo %s,


wie angefordert, erhalten Sie hiermit einen Link zu Ihrem Benutzerprofil f&uuml;r unsere Coummnity. Bitte vergessen Sie nicht, ein neues, individuelles Passwort in Ihrem Benutzerprofil festzulegen.

Benutzen Sie bitte den folgenden Link, um sich direkt einzuloggen:
%s

Sollten Sie Ihr Passwort nicht vergessen haben und trotzdem diese E-Mail erhalten haben, brauchen Sie nichts zu tun.

Beachten Sie au&szlig;erdem bitte, das Ihr Browser die Annahme von Cookies akzeptieren muss, damit Sie sich einloggen k&ouml;nnen!
'.$dontanswer,


#USER REGISTRATION
'registration_mail_title'						=> 'Registrierung f&uuml;r die %s Website',
'registration_mail_body_activate_true'			=> 'Benutzen Sie bitte diesen Link, um sich direkt anzumelden: 

%s

Sollten Sie Schwierigkeiten haben oder der Link nicht korrekt dargestellt werden, k&ouml;nnen Sie sich hier direkt auf unserer Website anmelden:

%s

Benutzen Sie dann bitte die folgenden Daten:',#1.mail link, 2.login link


'registration_mail_body_activate_false'			=> 'Ihr Benutzeraccount wird umgehend von einem der %s Administratoren gepr&uuml;ft. Sie erhalten nach der Freischaltung eine weitere E-Mail als Benachrichtigung.

Die Daten Ihres Benutzeraccounts mit der ID %s lauten:',#1. sitename, 2.userid

'registration_mail_body'						=> 'Willkommen %s,


Ihre Registrierung f&uuml;r die %s Website war erfolgreich und ein Benutzerkonto wurde f&uuml;r Sie angelegt.

%s

Benutzername:     %s
Passwort:         %s
Aktivierungscode: %s

Merken Sie sich dieses Passwort oder legen Sie ein individuelles Passwort in Ihrem Benutzerprofil fest.

Beachten Sie bitte, dass Ihr Browser die Annahme von Cookies akzeptieren muss, damit Sie sich einloggen k&ouml;nnen!



Mit freundlichen Gr&uuml;&szlig;en

Das %s-Team
%s',#1.usernick,2.sitename,3.mailaddtext,4.loginname,5.password,6.uid,7.sitename,8.siteurl


'registration_mail_body_novalidation'			=> 'Willkommen %s,


Ihre Registrierung f&uuml;r die %s Website war erfolgreich und ein Benutzerkonto wurde f&uuml;r Sie angelegt.

%s

Benutzername: %s
Passwort:     %s

Merken Sie sich dieses Passwort oder legen Sie ein individuelles Passwort in Ihrem Benutzerprofil fest.

Beachten Sie bitte, dass Ihr Browser die Annahme von Cookies akzeptieren muss, damit Sie sich einloggen k&ouml;nnen!




Mit freundlichen Gr&uuml;&szlig;en


Das %s-Team
%s',#1.nickname 2.sitename 3.mailaddtext 4. loginname, 5.password, 6.sitename 7.siteurl



'registration_mail_notify_title'				=> '%s - Neuer Benutzer: %s',#1.sitename,2.username
'registration_mail_notify_body'					=> 'Ein neuer Benutzer hat sich soeben auf %s registriert.

Benutzername: %s
E-Mail-Adresse: %s
ID: %s


Benutzen Sie den folgenden Link um zu seinem Benutzerprofil zu gelangen: %s',#1.sitename,2.username,3.emailaddress,4.userid,5.link


#USER LOST PASSWORD
'lostpassword_mail_title'						=> 'Passwort vergessen f&uuml;r die %s Website',#sitename
'lostpassword_mail_text'						=> 'Hallo %s,


Sollten Sie diese E-Mail nicht angefordert haben bzw. Ihr Passwort nicht vergessen haben, k&ouml;nnen Sie diese E-Mail l&ouml;schen. Sie brauchen nichts weiter tun, Ihr Passwort wurde nicht ver&auml;ndert.

Wenn Sie diese E-Mail angefordert haben, erhalten Sie hiermit einen Zugangscode um ein neues Passwort f&uuml;r die %s Website festzulegen. Klicken Sie hierzu auf den folgenden Direktlink.

%s

Ihr Benutzername f&uuml;r die Anmeldung lautet: %s



Mit freundlichen Gr&uuml;&szlig;en


Das %s Team
%s',#1.usernick 2.sitename, 3.link, 4.username, 5.sitename, 6.siteurl

#USER MAILER
'user_mailer_body'								=> '%s


Diese Nachricht wurde Ihnen &uuml;ber %s gesendet.
%s',


#PN NOTIFY
'pncenter_mail_notify'							=> 'Hallo %s,

auf %s liegt eine neue Kurznachricht von %s f&uuml;r Sie bereit.


Betreff der Nachricht: %s


Klicken Sie bitte auf den folgenden Link um sich die Nachricht anzusehen:
%s


Beachten Sie bitte, dass Sie sich ggf. erst einloggen m&uuml;ssen damit Sie die Nachricht lesen k&ouml;nnen.',
#1.usernick, 2.sitename, 3.author, 4.pn title 5.link


#CONTENT SUBMITED
'content_submit_notify_mailtitle'				=> 'Neue Einsendung auf %s: %s',
'content_submit_notify_mail'					=> 'Es wurde auf %s ein %s eingesendet und liegt zur &uuml;berpr&uuml;fung und Bearbeitung bereit.

Autor: %s
E-Mail-Adresse: %s
Titel: %s


Benutzen Sie den folgenden Link, um die Einsendung in der Administration zu kontrollieren: %s',

#GUESTBOOK COMMENT NOTIFY
'guestbook_comment_mail_title'					=> 'Kommentar zu Ihrem G&auml;stebucheintrag auf %s',#sitename
'guestbook_comment_mail_text'					=> 'Hallo %s,


Gerade wurde ein Kommentar zu Ihrem G&auml;stebucheintrag auf %s verfasst.

%s schrieb:

%s


Klicken Sie bitte auf den folgenden Link um sich den Kommentar auf %s anzusehen:
%s



Mit freundlichen Gr&uuml;&szlig;en


Das %s Team
%s',#1.gbook author2.sitename,3.comment author,4.comment_text,5.sitename,6.link,7.sitename,8.siteurl

#GUESTBOOK NOTIFY MAIL
'guestbook_notify_mail_text'					=>'Auf %s hat %s einen neuen G&auml;stebucheintrag erstellt.

Klick Sie auf den folgenden Link um sich den Eintrag anzusehen: %s',#!.sitename,2.author,3.link
);
?>