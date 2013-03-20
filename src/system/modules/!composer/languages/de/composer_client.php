<?php

/**
 * Headlines
 */
$GLOBALS['TL_LANG']['composer_client']['errors_headline']           = 'Systemvoraussetzungen';
$GLOBALS['TL_LANG']['composer_client']['composer_install_headline'] = 'Composer Installation';
$GLOBALS['TL_LANG']['composer_client']['installed_headline']        = 'Installierte Pakete';
$GLOBALS['TL_LANG']['composer_client']['search_headline']           = 'Suchergebnisse';
$GLOBALS['TL_LANG']['composer_client']['install_headline']          = 'Paket installieren';
$GLOBALS['TL_LANG']['composer_client']['editor_headline']           = 'Expertenmodus';

/**
 * References
 */
$GLOBALS['TL_LANG']['composer_client']['ftp_mode']           = 'Der Safe-Mode-Hack wird nicht unterstützt.<br>Richten Sie Contao so ein, dass es ohne SMH lauffähig ist.<br>&rarr; <a href="http://de.contaowiki.org/Safemode_Hack" target="_blank">Artikel zum SMH im Contao Wiki</a>';
$GLOBALS['TL_LANG']['composer_client']['php_version']        = 'Für den Einsatz des Composer Client wird <strong>PHP 5.3.4</strong> vorausgesetzt, sie verwenden <strong>PHP %s</strong>.<br>Bitte aktualisieren Sie auf eine aktuellere PHP Version.';
$GLOBALS['TL_LANG']['composer_client']['curl_missing']       = 'Zum herunterladen von Dateien wird curl benötigt.<br>Bitte installieren Sie das PHP Modul <a href="http://php.net/curl" target="_blank">curl</a>.';
$GLOBALS['TL_LANG']['composer_client']['composer_missing']   = 'Die Composer Library ist nicht (vollständig) installiert.<br>Drücken Sie auf <strong>Composer installieren</strong> um Composer und alle Abhängigkeiten zu installieren.';
$GLOBALS['TL_LANG']['composer_client']['install_composer']   = 'Composer installieren';
$GLOBALS['TL_LANG']['composer_client']['search_placeholder'] = 'Paketname oder Suchbegriff';

/**
 * Package listing
 */
$GLOBALS['TL_LANG']['composer_client']['package_name']              = 'Paket';
$GLOBALS['TL_LANG']['composer_client']['package_version']           = 'Version';
$GLOBALS['TL_LANG']['composer_client']['package_requested_version'] = 'Angeforderte Version';
$GLOBALS['TL_LANG']['composer_client']['package_installed_version'] = 'Installierte Version';
$GLOBALS['TL_LANG']['composer_client']['package_keywords']          = 'Keywords';
$GLOBALS['TL_LANG']['composer_client']['package_reference']         = 'Referenz';
$GLOBALS['TL_LANG']['composer_client']['package_type']              = 'Typ';
$GLOBALS['TL_LANG']['composer_client']['package_support']           = 'Support';
$GLOBALS['TL_LANG']['composer_client']['package_support_email']     = 'E-Mail';
$GLOBALS['TL_LANG']['composer_client']['package_support_issues']    = 'Ticketsystem';
$GLOBALS['TL_LANG']['composer_client']['package_support_wiki']      = 'Wiki';
$GLOBALS['TL_LANG']['composer_client']['package_support_irc']       = 'IRC Chat';
$GLOBALS['TL_LANG']['composer_client']['package_support_source']    = 'Quellcode';
$GLOBALS['TL_LANG']['composer_client']['package_source']            = 'Quellcode';
$GLOBALS['TL_LANG']['composer_client']['package_authors']           = 'Entwickler';
$GLOBALS['TL_LANG']['composer_client']['package_homepage']          = 'Homepage';
$GLOBALS['TL_LANG']['composer_client']['package_requires']          = 'Abhängigkeiten';
$GLOBALS['TL_LANG']['composer_client']['package_suggests']          = 'Empfehlungen';
$GLOBALS['TL_LANG']['composer_client']['package_provides']          = 'Provides';
$GLOBALS['TL_LANG']['composer_client']['package_conflicts']         = 'Konflikte';
$GLOBALS['TL_LANG']['composer_client']['package_replaces']          = 'Ersetzt';
$GLOBALS['TL_LANG']['composer_client']['no_requires']               = 'keine Abhängigkeiten';
$GLOBALS['TL_LANG']['composer_client']['no_suggests']               = 'keine Empfehlungen';
$GLOBALS['TL_LANG']['composer_client']['no_provides']               = 'keine Provides';
$GLOBALS['TL_LANG']['composer_client']['no_conflicts']              = 'keine Konflikte';
$GLOBALS['TL_LANG']['composer_client']['no_replaces']               = 'keine Ersetzungen';
$GLOBALS['TL_LANG']['composer_client']['not_installed']             = 'Installation angefordert';
$GLOBALS['TL_LANG']['composer_client']['install_via']               = 'via %s: %s';
$GLOBALS['TL_LANG']['composer_client']['dependency_of']             = 'Abhängigkeit von %s';
$GLOBALS['TL_LANG']['composer_client']['installed_in']              = 'installiert in Version %s';
$GLOBALS['TL_LANG']['composer_client']['no_releasedate']            = '-';

/**
 * Versions
 */
$GLOBALS['TL_LANG']['composer_client']['version_exakt']    = 'exakte Version %s';
$GLOBALS['TL_LANG']['composer_client']['version_bugfix']   = 'Bugfix Releases %s';
$GLOBALS['TL_LANG']['composer_client']['version_feature']  = 'Feature Releases %s';
$GLOBALS['TL_LANG']['composer_client']['version_upstream'] = 'Upstream Releases %s';

/**
 * Message
 */
$GLOBALS['TL_LANG']['composer_client']['composerUpdateRequired']   = 'Die Composer Version ist älter als 30 Tage, ein Update wird dringend empfohlen.';
$GLOBALS['TL_LANG']['composer_client']['composerUpdated']          = 'Composer wurde aktualisiert!';
$GLOBALS['TL_LANG']['composer_client']['noSearchResult']           = 'Es wurde kein package für <em>%s</em> gefunden!';
$GLOBALS['TL_LANG']['composer_client']['noInstallationCandidates'] = 'Keine Kandidaten für <em>%s</em> gefunden!';
$GLOBALS['TL_LANG']['composer_client']['unknown_license']          = 'unbekannte Lizenz';
$GLOBALS['TL_LANG']['composer_client']['added_candidate']          = 'Paket %s in Version %s hinzugefügt. Aktualisieren Sie die Pakete um die Änderungen anzuwenden.';
$GLOBALS['TL_LANG']['composer_client']['removeCandidate']          = 'Paket %s wurde entfernt. Aktualisieren Sie die Pakete um die Änderungen anzuwenden.';
$GLOBALS['TL_LANG']['composer_client']['configValid']              = 'Die Konfiguration ist valid.';
$GLOBALS['TL_LANG']['composer_client']['removePackage']            = 'Paket entfernen';
$GLOBALS['TL_LANG']['composer_client']['confirmRemove']            = 'Möchten Sie das Paket %s wirklich entferen?';
$GLOBALS['TL_LANG']['composer_client']['toBeRemoved']              = 'wird entfernt';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['composer_client']['update_database'] = 'Datenbank aktualisieren';
$GLOBALS['TL_LANG']['composer_client']['experts_mode']    = 'Expertenmodus';
$GLOBALS['TL_LANG']['composer_client']['update_composer'] = 'Composer aktualisieren';
$GLOBALS['TL_LANG']['composer_client']['search']          = 'Suchen';
$GLOBALS['TL_LANG']['composer_client']['mark_to_install'] = 'Paket zur Installation vormerken';
$GLOBALS['TL_LANG']['composer_client']['update']          = 'Pakete aktualisieren';
$GLOBALS['TL_LANG']['composer_client']['save']            = 'Speichern';
