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


return array(
'eventtitle_unknown_error'		=> 'Unbekannter Fehler',
'event_unknown_error'			=> 'Es ist ein unbekannter Fehler aufgetreten. Bitte gehen Sie zu Ihrem Ausgangspunkt zur&uuml;ck und versuchen Sie es erneut.',

'event_moving_link'				=> '<br /><br /><span class="small bold">Sie werden in wenigen Sekunden weitergeleitet, wenn Sie nicht warten m&ouml;chten, <a href="%s">klicken Sie bitte hier</a>.</span>',

#page_not_found
'eventtitle_page_not_found'		=> 'Seite nicht gefunden',
'event_page_not_found'			=> 'Die von Ihnen angew&auml;hlte Seite bzw. Sektion ist nicht verf&uuml;gbar!',

#access_refused
'eventtitle_access_refused'		=> 'Zugriff verweigert',
'event_access_refused'			=> '<p>Der Zugriff auf die von Ihnen angew&auml;hlte Seite wurde unterbunden. Dies kann einen der folgenden Gr&uuml;nde haben:</p>
<ul><li>Sie haben sich nicht angemeldet oder sind noch nicht registriert.</li>
<li>Sie verf&uuml;gen nicht &uuml;ber die notwendigen Rechte, um diese Seite betreten zu k&ouml;nnen.</li>
<li>Ihre Sitzung wurde wegen zu langer Inaktivit&auml;t beendet.</li>
<li>Jemand anderes verwendet Ihr Benutzerkonto.</li></ul>',

#login (success)
'eventtitle_login'				=> 'Anmeldung erfolgreich',
'event_login'					=> 'Sie haben sich erfolgreich angemeldet. Herzlich willkommen <b>'.pkGetUservaluef('nick').'</b>!<br /><br /><span class="small bold">Sie werden in wenigen Sekunden weitergeleitet, wenn Sie nicht warten m&ouml;chten, <a href="%s">klicken Sie bitte hier</a>.</span>',

#login (failed)
'eventtitle_login_false'		=> 'Anmeldung fehlgeschlagen',
'event_login_false'				=> '<p>Ihre Anmeldung konnte nicht erfolgreich durchgef&uuml;hrt werden.</p><p>F&uuml;r die Nutzung dieses Onlineangebotes ist es erforderlich, dass Cookies in Ihrem Browser/Firewall aktiviert sind.<br />L&ouml;schen Sie ggf. vor der n&auml;chsten Anmeldung alle Cookies dieser Website.</p><p><a href="'.pkLink('login').'" class="small bold">Klicken Sie bitte hier, um sich erneut anzumelden.</a></p>',

#logout (success)
'eventtitle_logout'				=> 'Abmeldung',
'event_logout'					=> 'Sie haben sich erfolgreich abgemeldet. Auf Wiedersehen, bis zu Ihrem n&auml;chsten Besuch.',

#logout (failed)
'eventtitle_logout_false'		=> 'Abmeldung fehlgeschlagen',
'event_logout_false'			=> '<p>Ihre Abmeldung konnte nicht erfolgreich durchgef&uuml;hrt werden.</p><p>F&uuml;r die Nutzung dieses Onlineangebotes ist es erforderlich, dass Cookies in Ihrem Browser/Firewall aktiviert sind.<br />L&ouml;schen Sie ggf. vor der n&auml;chsten Anmeldung alle Cookies dieser Website.</p><p><a href="'.pkLink('','','logout=1').'" class="small bold">F&uuml;r einen neuen Versuch sich abzumelden, klicken Sie bitte hier.</a></p>',

#registration successful
'eventtitle_registration_successful' => 'Registrierung erfolgreich',
'event_registration_successful'	=> 'Ihre Registrierung war erfolgreich.<br /><br />In Ihrem E-Mail-Postfach finden Sie eine Best&auml;tigungsmail mit einem Link zu Ihrem Benutzerprofil.',

#profileupdate
'eventtitle_profileupdate'		=> 'Daten gespeichert',
'event_profileupdate'			=> 'Die Anpassung Ihrer Profil-Angaben wurde erfolgreich vorgenommen.',
#6
'eventtitle_password_sent'=>'E-Mail gesendet',
'event_password_sent'=>'An Ihre E-Mail-Adresse wurde eine E-Mail mit einem Zugangscode zur &Auml;nderung Ihres Passwortes gesendet. Folgen Sie bitten den Anweisungen die Sie in der E-Mail finden.',
#7
'eventtitle_guestbook'=>'Vielen Dank f&uuml;r Ihren Eintrag',
'event_guestbook'=>'Vielen Dank f&uuml;r Ihren Eintrag in das G&auml;stebuch.<br /><br /><span class="small bold">Sie werden weitergeleitet. Wenn Sie nicht warten m&ouml;chten, <a href="%s">klicken Sie bitte hier</a>.</span>',
#10
'eventtitle_mailaddress_invalid'=>'E-Mail-Adresse ung&uuml;ltig',
'event_mailaddress_invalid'=>'Sie haben keine g&uuml;ltige E-Mail-Adresse angegeben.<br />Bitte versuchen Sie es erneut.',
#12
'eventtitle_comment_data_missing'=>'Fehler',
'event_comment_data_missing'=>'Autor und Kommentar m&uuml;ssen eingetragen werden!',
#13
'eventtitle_constribution_thank'=>'Vielen Dank f&uuml;r Ihren Beitrag',
'event_constribution_thank'=>'Vielen Dank f&uuml;r Ihren Beitrag.<br /><br /><span class="small">Sie werden weitergeleitet. Wenn Sie nicht warten m&ouml;chten, <a href="%s">klicken Sie bitte hier</a>.</span>',
#14
'eventtitle_article_not_available'=>'Artikel nicht verf&uuml;gbar',
'event_article_not_available'=>'Der von Ihnen gew&uuml;nschte Artikel ist nicht verf&uuml;gbar.<br /><br />M&ouml;glicherweise hat sich die URL ge&auml;ndert oder der Artikel wird zur Zeit &uuml;berarbeitet.',
#14.2
'eventtitle_download_not_available'=>'Download nicht verf&uuml;gbar',
'event_download_not_available'=>'Der von Ihnen gew&uuml;nschte Download ist nicht verf&uuml;gbar.<br /><br />M&ouml;glicherweise hat sich die URL ge&auml;ndert oder der Download wird zur Zeit &uuml;berarbeitet.',
#17
'eventtitle_guestbook_entry_exists'=>'G&auml;stebucheintrag',
'event_guestbook_entry_exists'=>'Ihr Beitrag zum $sitename-G&auml;stebuch wurde bereits eingetragen!',
#19
'eventtitle_submit_info'=>'Vielen Dank f&uuml;r Ihren Beitrag',
'event_submit_info'=>'Vielen Dank f&uuml;r Ihren Beitrag<br /><br />&Uuml;ber eine eventuelle Aufnahme in unsere Datenbank entscheidet der hierf&uuml;r zust&auml;ndige Administrator.<br />Nach der Pr&uuml;fung informieren wie Sie, ob Ihr Beitrag aufgenommen werden konnte.',
#20
'eventtitle_messages_denied'=>'Keine Nachrichten',
'event_messages_denied'=>'Dieser Benutzer w&uuml;nscht keine Kurznachrichten.',
#21
'eventtitle_registration_disabled'=>'Registrierung nicht m&ouml;glich',
'event_registration_disabled'=>'Eine Registrierung auf dieser Website ist nicht m&ouml;glich.<br /><br />Bitte wenden Sie sich an den <a href="'.pkLink('contact','','contact_subject=Mitgliedschaft').'">Administrator</a> von '.pkGetConfigf('site_name').', wenn Sie an einer Mitgliedschaft interessiert sind.',

'eventtitle_registration_denied' => 'Erneute Registrierung nicht m&ouml;glich',
'event_registration_denied' => 'Sie haben sich bereits auf dieser Website registriert.',
#23
'eventtitle_function_disabled'=>'Funktion deaktiviert',
'event_function_disabled'=>'Die von Ihnen gew&uuml;nschte Funktion wurde durch den Administrator deaktiviert.',
#24
'eventtitle_account_created'=>'Benutzerkonto erstellt',
'event_account_created'=>'Ihr Benutzerkonto wurde erfolgreich erstellt und wird umgehend von einem Administrator gepr&uuml;ft.<br /><br />Sie werden per E-Mail benachrichtigt, sobald Ihr Benutzerkonto freigeschaltet wurde.',
#25
'eventtitle_webmaster_message_sent'=>'Nachricht gesendet',
'event_webmaster_message_sent'=>'Vielen Dank f&uuml;r Ihre Nachricht.<br /><br />Die Nachricht wurde an den Administrator von '.pkGetConfigf('site_name').' gesendet.<br />Dieser wird sich mit Ihnen in K&uuml;rze in Verbindung setzen.<br /><br /><span class="small bold">Sie werden in wenigen Sekunden weitergeleitet. Wenn Sie nicht warten m&ouml;chten, <a href="%s">klicken Sie bitte hier</a></span>',
#26
'eventtitle_suggestion_sent'=>'Empfehlung gesendet',
'event_suggestion_sent'=>'Vielen Dank!<br /><br />Ihre Empfehlung wurde erfolgreich gesendet.',
#27
'eventtitle_account_inactive'=>'Benutzerkonto nicht aktiviert',
'event_account_inactive'=>'<p>Ihr Benutzerkonto wurde noch nicht aktiviert.</p><p>Sie werden von uns per E-Mail &uuml;ber die Aktivierung Ihres Benutzerkontos benachrichtigt.</p>',
#28
'eventtitle_forum_closed'=>'Forum geschlossen',
'event_forum_closed'=>'Das Forum wurde durch den Webmaster geschlossen.',
#29
'eventtitle_mainadmin_account_delete'=>'Aktion verweigert',
'event_mainadmin_account_delete'=>'Aus Sicherheitsgr&uuml;nden darf das Benutzerkonto des Hauptadministrators nicht gel&ouml;scht werden.',
#30 
'eventtitle_account_deleted'=>'Benutzerkonto gel&ouml;scht',
'event_account_deleted'=>'Ihr Benutzerkonto wurde gel&ouml;scht.',
#31
'eventtitle_search_noresult'=>'Suche',
'event_search_noresult'=>'Ihre Suchanfrage liefert leider keine Ergebnisse.',
#32
'eventtitle_firstlogin'=>'Anmeldung erfolgreich',
'event_firstlogin'=>'Ihre Anmeldung war erfolgreich. Sie k&ouml;nnen nun Ihr Passwort in Ihrem Benutzerprofil &auml;ndern.',
#33
'eventtitle_email_contact_undesired'=>'E-Mail-Versand',
'event_email_contact_undesired'=>'Der Benutzer w&uuml;nscht keinen E-Mail-Kontakt.',
#34
'eventtitle_email_sent'=>'Nachricht gesendet',
'event_email_sent'=>'Ihre Nachricht wurde erfolgreich gesendet.',
#35
'eventtitle_email_error'=>'Nachricht nicht gesendet',
'event_email_error'=>'Ein Fehler ist w&auml;hrend des Versands Ihrer Nachricht aufgetreten. Die E-Mail konnte nicht abgeschickt werden!<br /><br />Bitte versuchen Sie es erneut.',
#36
'eventtitle_thread_closed'=>'Thema geschlossen',
'event_thread_closed'=>'Dieses Thema wurde geschlossen.<br />Antworten sind nicht mehr m&ouml;glich.',
'eventtitle_thread_does_not_exists'=>'Thema nicht verf&uuml;gbar',
'event_thread_does_not_exists'=>'Das von Ihnen angew&auml;hlte Forenthema wurde nicht gefunden. M&ouml;glicherweise wurde das Thema gel&ouml;scht.',
#37
'eventtitle_forum_closed'=>'Forum geschlossen',
'event_forum_closed'=>'Dieses Forum wurde geschlossen.<br />Das Erstellen von Antworten und neuen Themen ist nicht m&ouml;glich.',
#38
'eventtitle_entry_repeat'=>'Antworten nicht m&ouml;glich',
'event_entry_repeat'=>'Sie d&uuml;rfen nicht noch einmal antworten. Der letzte Beitrag in diesem Thema stammt bereits von Ihnen.<br />Editieren Sie Ihren letzten Beitrag oder warten Sie bis jemand anderes antwortet.',
#39
'eventtitle_buddy_addself'=>'Fehler',
'event_buddy_addself'=>'Sie k&ouml;nnen sich nicht selber zu Ihren Freunden hinzuf&uuml;gen.',
#40
'eventtitle_profile_update_disabled'=>'Profil&auml;nderung nicht m&ouml;glich',
'event_profile_update_disabled'=>'Sie d&uuml;rfen Ihr Profil nicht bearbeiten!<br />Diese M&ouml;glichkeit wurde f&uuml;r Ihr Benutzerkonto gesperrt.',
#41
'eventtitle_account_marked_deleted'=>'L&ouml;schung des Benutzerkontos',
'event_account_marked_deleted'=>'Ihr Benutzerkonto wurde f&uuml;r eine L&ouml;schung markiert.<br /><br />Die L&ouml;schung wird umgehend durch einen der Administratoren erfolgen.',
#42
'eventtitle_name_in_use'=>'Fehler',
'event_name_in_use'=>'Verwenden Sie bitte einen anderen Namen. Der von Ihnen gew&auml;hlte Name wird bereits verwendet oder darf nicht benutzt werden!<br />Bitte beachten Sie dabei, dass der verwendete Name nur zwischen '.pkGetConfig('user_namemin').' und '.pkGetConfig('user_namemax').' Zeichen lang sein darf.',
#43
'eventtitle_privatemessage_not_found'=>'Nachricht nicht gefunden',
'event_privatemessage_not_found'=>'Die von Ihnen gew&uuml;nschte private Nachricht konnte nicht gefunden werden.',
#44
'eventtitle_privatemessages_disabled'=>'Kurznachrichten deaktiviert',
'event_privatemessages_disabled'=>'<p>Sie haben die Nutzung der privaten Nachrichten abgeschaltet. Sie k&ouml;nnen erst private Nachrichten versenden, wenn Sie in den &quot;Erweiterten Einstellungen&quot; Ihres Benutzerprofils die Nutzung reaktivieren.</p><p><a href="include.php?path=userprofile&mode=options">Klicken Sie hier, wenn sie dies jetzt vornehmen m&ouml;chten.</a></p>',
#45
'eventtitle_comment_wait_loop'=>'Bitte warten',
'event_comment_wait_loop'=>'Sie m&uuml;ssen nach dem Schreiben eines Kommentars '.pkGetConfigf('comment_floodctrl').' Minute(n) warten, bis Sie einen weiteren Kommentar schreiben d&uuml;rfen.',
#46
'eventtitle_comment_length'=>'Kommentar zu lang',
'event_comment_length'=>'Der von Ihnen eingegebene Kommentar ist zu lang. Erlaubt sind maximal '.pkGetConfigf('comment_maxchars').' Zeichen!',
#47
'eventtitle_download_not_found'=>'Download nicht gefunden',
'event_download_not_found'=>'Leider konnte der von Ihnen gew&uuml;nschte Download nicht gefunden werden.',

'eventtitle_multi_emailaddresses'=>'Fehler',
'event_multi_emailaddresses'=>'Im Formular ist ein Fehler aufgetreten.<br /><br />Bitte geben Sie maximal <strong>eine richtige</strong> E-Mail-Adresse ein!',
'eventtitle_already_logged_in'=>'Angemeldet',
'event_already_logged_in'=>'Sie haben sich bereits angemeldet.',
'eventtitle_invalid_threadid'=>'Ung&uuml;ltige Themen-ID',
'event_invalid_threadid'=>'Das von Ihnen aufgerufene Thema existiert nicht.',
'eventtitle_email_unsubscribed'=>'E-Mail-Adresse ausgetragen',
'event_email_unsubscribed'=>'Die E-Mail-Adresse wurde aus unserer Datenbank gel&ouml;scht.',
'eventtitle_no_favorites'=>'Keine Favoriten gespeichert',
'event_no_favorites'=>'Es sind keine Themen in Ihrer pers&ouml;nlichen Favoritenliste gespeichert.',
'eventtitle_securitycode_invalid'=>'Sicherheitscode ung&uuml;ltig',
'event_securitycode_invalid'=>'Der Sicherheitscode wurde fehlerhaft eingegeben.',
'eventtitle_searchterm_too_short'=>'Suchbegriff zu kurz',
'event_searchterm_too_short'=>'Der von Ihnen eingegebene Suchbegriff ist zu kurz gehalten. Bitte w&auml;hlen Sie einen anderen Begriff der mindestens '.pkGetConfigF('search_min_length').' Zeichen lang ist.',
'eventtitle_searchresult_limited'=>'Suche eingegrenzt',
'event_searchresult_limited'=>'Der von Ihnen eingegebene Suchbegriff hat mehr als '.pkGetConfigF('search_max').' Treffer ergeben. Es werden Ihnen nur die ersten '.pkGetConfigF('search_max').' Suchergebnisse angezeigt. F&uuml;hren Sie die Suche erneut aus, um ein genaueres Ergenis zu erhalten.',
'eventtitle_account_not_displayable'=>'Profil nicht anzeigbar',
'event_account_not_displayable'=>'Das angew&auml;hlte Benutzerprofil kann nicht angezeigt werden. Das Benutzerkonto ist nicht vorhanden oder wurde m&ouml;glicherweise noch nicht aktiviert.',
'eventtitle_entries_incomplete'=>'Eintragungen unvollst&auml;ndig',
'event_entries_incomplete'=>'Ihre Eintragungen sind leider nicht vollst&auml;ndig. Bitte komplettieren Sie Ihre Angaben und f&uuml;llen Sie alle Felder aus.',
'eventtitle_password_changed'=>'Passwort ge&auml;ndert',
'event_password_changed'=>'Ihr Passwort wurde erfolgreich ge&auml;ndert. Sie k&ouml;nnen sich nun mit Ihrem neuem Passwort anmelden.',

'eventtitle_profileupdate_wrong_password'=>'Falsches Passwort',
'event_profileupdate_wrong_password'=>'Das von Ihnen angegebene, aktuelle Passwort war falsch. Bitte wiederholen Sie Ihre Angaben.',
'eventtitle_profileupdate_nickname_in_use'=>'Spitzname bereits vergeben',
'event_profileupdate_nickname_in_use'=>'Beim Update Ihres Benutzerprofils ist leider ein Fehler aufgetreten!<br /><br />Der von Ihnen ausgew&auml;hlte Spitzname, wird leider schon von einem anderem Benutzer verwendet. Bitte w&auml;hlen Sie einen anderen Namen aus',
'eventtitle_profileupdate_nickname_empty'=>'Keinen Spitzname angegeben ',
'event_profileupdate_nickname_empty'=>'Beim Update Ihres Benutzerprofils ist leider ein Fehler aufgetreten!<br /><br />Sie haben keinen Spitznamen ausgew&auml;hlt. Diese Angabe ist notwendig.',
'eventtitle_profileupdate_passwords_unequal'=>'Passw&ouml;rter ungleich',
'eventtitle_profileupdate_nickname_invalid'=>'Nickanme ung&uuml;ltig',
'event_profileupdate_nickname_invalid'=>'Verwenden Sie bitte einen anderen Nicknamen. Der von Ihnen gew&auml;hlte Nickname wird bereits verwendet oder darf nicht benutzt werden!<br />Bitte beachten Sie dabei, dass der verwendete Name nur zwischen '.pkGetConfig('user_namemin').' und '.pkGetConfig('user_namemax').' Zeichen lang sein darf.',
'event_profileupdate_passwords_unequal'=>'Beim Update Ihres Benutzerprofils ist leider ein Fehler aufgetreten!<br /><br />Das Passwort konnte nicht ge&auml;ndert werden, da die Angaben nicht &uuml;bereinstimmten.',
'eventtitle_profileupdate_email_empty'=>'Keine E-Mail-Adresse angegeben',
'event_profileupdate_email_empty'=>'Beim Update Ihres Benutzerprofils ist leider ein Fehler aufgetreten!<br /><br />Sie haben keine E-Mail-Adresse angegeben. Die Angabe einer g&uuml;ltigen E-Mail-Adresse ist notwendig.',
'eventtitle_profileupdate_email_invalid'=>'Ung&uuml;tige E-Mail-Adresse',
'event_profileupdate_email_invalid'=>'Beim Update Ihres Benutzerprofils ist leider ein Fehler aufgetreten!<br /><br />Die von Ihnen angegebene E-Mail-Adresse scheint nicht g&uuml;ltig zu sein. Bitte kontrollieren Sie Ihre Eingabe noch einmal.',
'eventtitle_profileupdate_email_in_use'=>'E-Mail-Adresse bereits in Verwendung',
'event_profileupdate_email_in_use'=>'Beim Update Ihres Benutzerprofils ist leider ein Fehler aufgetreten!<br /><br />Die von Ihnen angegebene E-Mail-Adresse ist bereits in Verwendung durch einen anderen Benutzer. Bitte w&auml;hlen Sie eine andere E-Mail-Adresse.',

'register_now'=>'Kein Benutzerkonto? Registrieren Sie sich jetzt!',
'username_or_password_lost'=>'Benutzername oder Passwort vergessen? Kein Problem!',
'activationcode'=>'Aktivierungscode',
'activationcode_description'=>'Aktivierungscode den Sie per E-Mail erhalten haben',

'login_error0'=>'Geben Sie bitte Ihren Benutzernamen und Ihr Passwort ein.',
'login_error1'=>'Sie haben keinen Benutzernamen und kein Passwort eingegeben!',
'login_error2'=>'Sie haben keinen Benutzernamen angegeben!',
'login_error3'=>'Sie haben kein Passwort eingegeben!',
'login_error4'=>'Sie haben einen falschen Benutzernamen oder ein falsches Passwort eingegeben!',
);
?>