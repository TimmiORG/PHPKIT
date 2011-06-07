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
'config_cookie_settings'					=> 'Cookie-Einstellungen',
'config_cookie_domain_label'				=> 'Cookie-Domain',
'config_cookie_domain_desc'					=> 'Die Domain, der das Cookie zur Verf&uuml;gung steht. Um das Cookie f&uuml;r alle Sub-Domains von &quot;beispiel.de&quot; verf&uuml;gbar zu machen, setzen Sie es auf &quot;.beispiel.de&quot;. Der &quot;.&quot; ist nicht zwingend erforderlich, erh&ouml;ht aber die Browser-Kompatibilit&auml;t. Ein setzen auf &quot;www.beispiel.de&quot; macht das Cookie nur in der &quot;www&quot; Sub-Domain verf&uuml;gbar.',
'config_cookie_path_label'					=> 'Cookie-Pfad',
'config_cookie_path_desc'					=> 'Der Pfad zu dem Server, auf welchem das Cookie verf&uuml;gbar sein wird. Ist er auf &quot;/&quot; gesetzt, wird das Cookie innerhalb der gesamten Domain verf&uuml;gbar. Auf &quot;/site/&quot; gesetzt steht der Cookie nur im Unterverzeichnis &quot;site&quot; zur Verf&uuml;gung.',

'config_site_forntpage_elements_label'		=> 'Elemente der Startseite',
'config_site_forntpage_elements_desc'		=> 'Tragen Sie hier die Pfadangabe, ausgehend vom Installationsverzeichnis, zu den Dateien ein,
												die auf der Startseite angezeigt werden sollen (angezeigt unter dem Begr&uuml;&szlig;ungstext, wenn aktiviert).<br />
												Systemseiten binden Sie unter Angabe des Dateinamens (ohne Dateiendung) ein,
												<a class="small" href="javascript:;" onClick="morelinkswindow(500,650,\'short\');">eine Auswahl finden Sie hier</a>.<br />
												<br />
												Mehrere Angaben m&ouml;glich. Je Zeile nur eine Angabe.',
'config_site_frontpage_link_label'			=> 'Link zur Startseite',
'config_site_frontpage_link_desc'			=> 'Hier k&ouml;nnen Sie die Verlinkung zur Startseite &auml;ndern. Wenn Sie keine Angabe machen, wird die Standardvorgabe verwendet.',
'config_site_frontpage_title_label'			=> 'Titel der Startseite',
'config_site_frontpage_title_desc'			=> 'Der Titel wird in der Titelleiste des Browsers angezeigt.',
'config_site_frontpage_welcome_text'		=> 'Begr&uuml;ssungstext',

'config_site_closure_label'					=> 'Seitenabschluss',
'config_site_closure_desc'					=> 'Hier k&ouml;nnen Sie HTML f&uuml;r den Seitenabschluss eingeben, z.B. zur Einbindung von externen Statistiken. Ihre Eingaben werden vor dem schlie&szlig;enden Body-Tag ausgegeben.',

'config_avatar_path_error' 					=> '<br /><span class="smallhighlight">Das gew&auml;hlte Verzeichnis erf&uuml;llt nicht alle Bedingungen!</span>',

'config_contact_page_title_label'			=> 'Titel des Kontaktformulars',
'config_contact_page_title_desc'			=> 'Tragen Sie hier einen Titel f&uuml;r das Kontaktformular ein. Geben Sie keinen Titel an, wird der Standardtitel verwendet.',
'config_contact_page_text_label'			=> 'Text des Kontaktformulars',
'config_contact_page_text_desc'				=> 'Hier k&ouml;nnen Sie einen Text eingeben der &uuml;ber dem Kontaktformular ausgegeben wird. Geben Sie keinen Text ein, wird der Standardtext angezeigt. Die Verwendung von HTML und BB-Code ist m&ouml;glich.',

'config_mail_footers'						=> 'E-Mail-Fu&szlig;zeilen',
'config_mail_footer_txt_label'				=> 'Fu&szlig;zeile f&uuml;r Text-E-Mails',
'config_mail_footer_txt_desc'				=> 'Wird an alle ausgehenden Text-E-Mails angeh&auml;ngt. Die Verwendung von HTML ist <strong>nicht</strong> m&ouml;glich.',
'config_mail_footer_html_label'				=> 'Fu&szlig;zeile f&uuml;r HTML-E-Mails',
'config_mail_footer_html_desc'				=> 'Wird an alle ausgehenden E-Mails angeh&auml;ngt die im HTML-Modus verschickt werden. Die Verwendung von HTML ist m&ouml;glich.',

'config_miscellaneous_settings'				=> 'Weitere Einstellungen',

'config_SMTP_settings'						=> 'SMTP-Einstellungen',
'config_SMTP_server_label'					=> 'SMTP-E-Mail-Server',
'config_SMTP_server_desc'					=> 'Ausgangsmailserver f&uuml;r SMTP-Verbindungen. Lassen Sie das Eingabefeld frei, um den Serverseitig eingestellten Mailserver zu verwenden. Beachten Sie bitte, dass passwortgesch&uuml;tzte SMTP-Verbindungen nicht unterst&uuml;tzt werden.',

'licencekey_valid'							=> 'Ein g&uuml;ltiger Lizenzschl&uuml;ssel wurde eingetragen.',
'licencekey_invalid'						=> 'Tragen Sie hier Ihren individuellen Lizenzschl&uuml;ssel, inklusive der Bindestriche, ein.',
'licencekey_explain_nokey'					=> '<p>F&uuml;r Ihre <strong>PHPKIT</strong> Installation wurde bisher kein g&uuml;ltiger Lizenzschl&uuml;ssel hinterlegt.</p>
<p>Bis zur Eintragung eines entsprechenden Lizenzschl&uuml;ssels steht Ihnen die Bannerverwaltung nicht zur Verf&uuml;gung. Bitte beachten Sie, dass die Anbringung von Verweisen auf kommerzielle Angebote ohne Privat- bzw. Basis-Lizenz nicht gestattet ist.</p>
<p>Sie haben die M&ouml;glichkeit, die oben genannte Form der Lizenzierung mit der Berechtigung zur Entfernung des sichtbaren Copyrightverweises zu kombinieren oder auch einzeln zu erwerben. Bei Eingabe eines g&uuml;ltigen Lizenzschl&uuml;ssels zur Copyrightentfernung, werden automatisch alle sichtbaren Copyrightverweise im &ouml;ffentlichen Bereich der Website f&uuml;r Sie entfernt.</p> 
<p>Sie besitzen noch keinen eigenen Lizenzschl&uuml;ssel oder m&ouml;chten einen verloren gegangenen Lizenzschl&uuml;ssel wieder herstellen lassen? Folgen Sie dem Verweis auf unsere Website, um Informationen zum Erwerb oder zur Wiederbeschaffung von Lizenzschl&uuml;sseln zu erhalten. Ihren individuellen Lizenzschl&uuml;ssel k&ouml;nnen Sie auf <a target="_blank" href="http://www.phpkit.com/de/bestellung">www.phpkit.com</a> bestellen.</p>',
'licencekey_explain_thank1'					=> '<p>Wir bedanken uns bei Ihnen f&uuml;r den Lizenzerwerb. Nachfolgend wird Ihnen der individuelle Lizenzschl&uuml;ssel dieser Installation angezeigt.</p><p>Die Lizenzierungspflicht f&uuml;r alle Nicht-Privatpersonen ist erf&uuml;llt. Mit dem Erwerb der Berechtigung zur kommerziellen Benutzung, wurde die Werbebanner-Verwaltung freigeschaltet.</p>',
'licencekey_explain_thank2'					=> '<p>Wir bedanken uns bei Ihnen f&uuml;r den Lizenzerwerb. Nachfolgend wird Ihnen der individuelle Lizenzschl&uuml;ssel dieser Installation angezeigt.</p><p>Die sichtbaren Copyrightvermerke im &ouml;ffentlichen Bereich, wurden f&uuml;r Sie deaktiviert.</p>',
'licencekey_explain_thank4'					=> '<p>Wir bedanken uns bei Ihnen f&uuml;r den Lizenzerwerb. Nachfolgend wird Ihnen der individuelle Lizenzschl&uuml;ssel dieser Installation angezeigt.</p><p>Die Lizenzierungspflicht f&uuml;r alle Nicht-Privatpersonen ist erf&uuml;llt. Mit dem Erwerb der Berechtigung zur kommerziellen Benutzung, wurde die Werbebanner-Verwaltung freigeschaltet. Die sichtbaren Copyrightvermerke im &ouml;ffentlichen Bereich, wurden f&uuml;r Sie deaktiviert.</p>',

'meta_config_author_label'					=> 'Autor',
'meta_config_author_desc'					=> 'Der Name des Autors der Website.',
'meta_config_author_email_label'			=> 'E-Mail-Adresse',
'meta_config_author_email_desc'				=> 'Die E-Mail-Adresse des Autors der Website.',
'meta_config_publisher_label'				=> 'Herausgeber',
'meta_config_publisher_desc'				=> 'Der Names des Herausgebers der Website.',
'meta_config_copyright_label'				=> 'Copyright',
'meta_config_copyright_desc'				=> 'Weisen Sie den geistigen Urheber des Inhalts der Website aus.',
'meta_config_keywords_label'				=> 'Schl&uuml;sselw&ouml;rter',
'meta_config_keywords_desc'					=> 'Geben Sie hier Schl&uuml;sselbegriffe an, die den Inhalt der Website beschreiben. Die W&ouml;rter und Wortgruppen sind durch Kommata zu trennen. Darf bis zu 1000 Zeichen enthalten.',
'meta_config_desc_label'					=> 'Beschreibung',
'meta_config_description_desc'				=> 'Tragen Sie hier eine Beschreibung des Inhalts der Website ein. Darf bis zu 200 Zeichen lang sein.',
'meta_config_favicon_label'					=> 'Favoriten-Icon',
'meta_config_favicon_desc'					=> 'Pfadangabe zu einer Icon-Datei, welches in der Adresszeile bzw. den Favoriten des Browsers angezeigt werden soll.',
'meta_config_robots_label'					=> 'Suchmaschinen-Robots',
'meta_config_robots_desc'					=> 'Legt fest, wie Suchmaschinen-Robots Ihre Seite indizieren. M&ouml;gliche Angaben: all, index, follow, nofollow, none etc.',
'meta_config_robots_revisit_label'			=> 'Wiederbesuch',
'meta_config_robots_revisit_desc'			=> 'Nach welchem Zeitraum sollen Suchmaschinen-Robots die Website wieder aufsuchen, zum Beispiel: 2 weeks oder 21 days.',
'meta_config_custom_label' 					=> 'Eigene Headerangaben',
'meta_config_custom_desc' 					=> 'Hier k&ouml;nnen Sie weitere Headerangaben eintragen. Diese werden im &lt;head&gt;-Bereich angezeigt. Hier k&ouml;nnen Sie weitere Meta-Tags definieren oder Javascripts verlinken.',

'config_time_settings'						=> 'Zeiteinstellungen',
'config_image_resize_settings'				=> 'Automatische Skalierung von Grafiken',
'config_image_resize_user_label'			=> 'Anpassung in Signaturen',
'config_image_resize_user_desc'				=> 'Wenn aktiviert, werden Grafiken in den Signaturen der Benutzer in der Gr&ouml;&szlig;e angepasst.',
'config_image_resize_text_label'			=> 'Anpassung in Benutzereingaben',
'config_image_resize_text_desc'				=> 'Wenn aktiviert, werden Grafiken in &ouml;ffentlichen Texten (<em class="sample">Bsp: Kommentare</em>)  in der Gr&ouml;&szlig;e angepasst. Grafiken in Texten der Inhaltsverwaltung werden nicht skaliert.',
'config_image_resize_size_label'			=> 'Maximale Abmessungen',
'config_image_resize_size_desc'				=> 'Legen Sie hier die Abmessungen f&uuml;r die H&ouml;he und Breite fest, auf die gr&ouml;&szlig;ere Grafiken angepasst werden sollen.',

'PHP_extension_gdlib_required'				=> 'PHP-Erweiterung gdLib ben&ouml;tigt',
);
?>