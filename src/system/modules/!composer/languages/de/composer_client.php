<?php

/**
 * Headlines
 */

$GLOBALS['TL_LANG']['composer_client']['added_candidate']               = 'Paket %s in Version %s hinzugefügt. Bitte aktualisieren Sie die Pakete um die Änderung zu übernehmen.';
$GLOBALS['TL_LANG']['composer_client']['check']                         = 'Kompatibilität prüfen';
$GLOBALS['TL_LANG']['composer_client']['clear_composer_cache']          = 'Composer Cache leeren';
$GLOBALS['TL_LANG']['composer_client']['close']                         = 'Schließen';
$GLOBALS['TL_LANG']['composer_client']['composerCacheCleared']          = 'Der Composer Cache wurde geleert.';
$GLOBALS['TL_LANG']['composer_client']['composerUpdateNecessary']       = 'Dies ist eine inkompatible Version der Composer Bibliothek. Es ist erforderlich, die Composer Bibliothek zu aktualisieren, ansonsten wird der Composer Client nicht wie erwartet funktionieren. ';
$GLOBALS['TL_LANG']['composer_client']['composerUpdateRequired']        = 'Die Composer Version ist älter als 30 Tage, bitte aktualisieren Sie Composer.';
$GLOBALS['TL_LANG']['composer_client']['composerUpdated']               = 'Composer wurde aktualisiert!';
$GLOBALS['TL_LANG']['composer_client']['composer_install_headline']     = 'Composer Installation';
$GLOBALS['TL_LANG']['composer_client']['composer_missing']              = 'Die Composer Bibliothek ist nicht komplett installiert. <br><strong>Composer installieren</strong> klicken, um Composer und alle Abhängigkeiten zu installieren.';
$GLOBALS['TL_LANG']['composer_client']['configValid']                   = 'Die Konfiguration ist gültig.';
$GLOBALS['TL_LANG']['composer_client']['confirmRemove']                 = 'Möchten Sie das Paket %s wirklich löschen?';
$GLOBALS['TL_LANG']['composer_client']['confirmRemovePackages']         = 'Sind Sie sicher, dass Sie die folgenden Pakete entfernen wollen?
%s';
$GLOBALS['TL_LANG']['composer_client']['could_not_disable_apc']         = 'APC konnte nicht deaktiviert werden.<br>
APC und Composer produzieren zusammen immer wieder Fehler, bitte <a href="http://php.net/apc" target="_blank">APC</a> deaktivieren.';
$GLOBALS['TL_LANG']['composer_client']['curl_missing']                  = 'cURL ist notwendig um Pakete herunterzuladen.<br>Bitte das PHP Modul <a href="http://php.net/curl" target="_blank">curl</a> installieren oder aktivieren. ';
$GLOBALS['TL_LANG']['composer_client']['databaseUpdated']               = 'Datenbank aktualisiert. Es wurden %d Anfragen ausgeführt.';
$GLOBALS['TL_LANG']['composer_client']['databaseUptodate']              = 'Die Datenbank ist aktuell.';
$GLOBALS['TL_LANG']['composer_client']['dependency_graph_headline']     = 'Abhängigkeitsgraph';
$GLOBALS['TL_LANG']['composer_client']['dependency_of']                 = 'Abhängigkeit von %s';
$GLOBALS['TL_LANG']['composer_client']['dependency_recursion']          = '(zirkulierende Abhängigkeit)';
$GLOBALS['TL_LANG']['composer_client']['detached']                      = 'Paket Update';
$GLOBALS['TL_LANG']['composer_client']['discard_changes_no']            = 'lokale Änderungen behalten (Update anhalten)';
$GLOBALS['TL_LANG']['composer_client']['discard_changes_stash']         = 'Update durchführen & lokale Änderung übernehmen';
$GLOBALS['TL_LANG']['composer_client']['discard_changes_yes']           = 'lokale Änderungen verwerfen';
$GLOBALS['TL_LANG']['composer_client']['download_impossible']           = 'Herunterladen ist nicht möglich. Entweder müssen Sie die PHP ZIP-Extension aktivieren oder Sie müssen sicherstellen das proc_open() unkomprimiert läuft.';
$GLOBALS['TL_LANG']['composer_client']['dry-run']                       = 'Testlauf';
$GLOBALS['TL_LANG']['composer_client']['editor_headline']               = 'Expertenmodus';
$GLOBALS['TL_LANG']['composer_client']['errors_headline']               = 'Systemanforderungen';
$GLOBALS['TL_LANG']['composer_client']['experts_mode']                  = 'Expertenmodus';
$GLOBALS['TL_LANG']['composer_client']['ftp_mode']                      = 'Der Safe-Mode-Hack wird nicht unterstützt.<br>Das Hosting-Paket muss so konfiguriert sein, dass ein Einsatz von Contao ohne Safe-Mode-Hack möglich ist. Weitere Infos findest Du im <br>&rarr; <a href="http://de.contaowiki.org/Safemode_Hack" target="_blank">Artikel über den Safe-Mode-Hack im Contao Wiki</a>';
$GLOBALS['TL_LANG']['composer_client']['incompatiblePackage']           = '(nicht kompatibel mit dieser Contao-Version)';
$GLOBALS['TL_LANG']['composer_client']['incompatiblePackageLong']       = 'Diese Paket-Version ist mit dieser Contao-Version nicht kompatibel!';
$GLOBALS['TL_LANG']['composer_client']['install_auto']                  = 'Auto';
$GLOBALS['TL_LANG']['composer_client']['install_composer']              = 'Composer installieren';
$GLOBALS['TL_LANG']['composer_client']['install_dist']                  = 'Distributionsarchiv';
$GLOBALS['TL_LANG']['composer_client']['install_headline']              = 'Paket installieren';
$GLOBALS['TL_LANG']['composer_client']['install_source']                = 'Quellen';
$GLOBALS['TL_LANG']['composer_client']['install_via']                   = 'von %s: %s';
$GLOBALS['TL_LANG']['composer_client']['installed_headline']            = 'Installierte Pakete';
$GLOBALS['TL_LANG']['composer_client']['installed_in']                  = 'Installiert in Version %s';
$GLOBALS['TL_LANG']['composer_client']['mark_and_install']              = 'Paket jetzt installieren';
$GLOBALS['TL_LANG']['composer_client']['mark_to_install']               = 'Paket für die Installation vormerken';
$GLOBALS['TL_LANG']['composer_client']['migrate']                       = 'Migrieren';
$GLOBALS['TL_LANG']['composer_client']['migrate_clean']['0']            = 'Pakete entfernen';
$GLOBALS['TL_LANG']['composer_client']['migrate_clean']['1']            = 'Bestehende Erweiterungen entfernen und mit einer frischen Installation starten.';
$GLOBALS['TL_LANG']['composer_client']['migrate_development']['0']      = 'Für Entwickler';
$GLOBALS['TL_LANG']['composer_client']['migrate_development']['1']      = 'Pakete werden aus den git, mercurial oder svn Quellen genutzt, Dateien werden als symlinks installiert.';
$GLOBALS['TL_LANG']['composer_client']['migrate_do']                    = 'Migration starten';
$GLOBALS['TL_LANG']['composer_client']['migrate_faq']                   = '
<h2>FAQ</h2>
<ul class="questions">
<li>
	<h3>Muss ich diese Erweiterungsverwaltung nutzen?</h3>
	Natürlich nicht. Jedoch veröffentlichen einige Entwickler neue Erweiterungen oder Funktionen nur noch über Composer. 
Im Zweifel verpasst Du wichtige Updates oder neue Erweiterungen, wenn Du Composer nicht nutzt.
</li>
<li>
	<h3>Kann ich Erweiterungen aus der aktuellen Erweiterungsverwaltung installieren?</h3>
	Ja. Alle öffentlichen Erweiterungen aus der bekannten Erweiterungsverwaltung werden synchronisiert und tragen das Prefix <em>contao-legacy/</em>).<br>
	<em>Bitte beachte jedoch das kommerzielle Erweiterungen aufgrund eingeschränkter Lizenzmöglichkeiten nicht mit Composer installiert werden können.
	Bitte erfrage in diesem Fall ComposerUnterstützung beim Entwickler.</em>
</li>
<li>
	<h3>Wird es eine neue Erweiterungsverwaltung geben?</h3>
	Ja, die neue Erweiterungsverwaltung befindet sich unter <a href="http://legacy-packages-via.contao-community-alliance.org/" target="_blank">legacy-packages-via.contao-community-alliance.org</a>.
	Aktuell ist dies eine normale packagist Installation, die wir jedoch in Kürze an unsere Bedürfnisse anpassen werden.
</li>
<li>
	<h3>Was ist Composer und die Composer Paketverwaltung?</h3>
	Die Antwort darauf würde hier den Rahmen sprengen. Mehr Infos zu Composer findest Du im <a href="http://de.contaowiki.org/Composer_Client" target="_blank">Contao Wiki</a>.
</li>
<li>
	<h3>Kann ich wieder zur alten Erweiterungsverwaltung wechseln?</h3>
	Ja. Gehe dazu in die Einstellung der Paketverwaltung und wähle: "switch back to old client".
</li>
<li>
	<h3>Ich habe Probleme mit der Paketverwaltung, wo finde ich Unterstützung?</h3>
	Composer ist ein Community Projekt. Hilfe erhältst Du hier:
	Du kannst im <a href="https://community.contao.org/de/forumdisplay.php?6-Entwickler-Fragen" target="_blank">Form</a>,
	dem offiziellen IRC channel <a href="irc://chat.freenode.net/%23contao.composer">#contao.composer</a>
	oder im <a href="https://github.com/contao-community-alliance/composer/issues" target="_blank">Ticket System</a> deine Fragen stellen.
</li>
</ul>';
$GLOBALS['TL_LANG']['composer_client']['migrate_intro']                 = '
<p>Lieber Nutzer, dies ist die neue Contao Paketverwaltung, basierend auf dem PHP Abhängigkeitsverwalter <a href="http://getcomposer.org/" target="_blank">Composer</a>.</p>
<p>Dies ist eine öffentliche Beta-Phase. Wir brauchen deine Hilfe diese Verwaltung zu testen und uns Feedback zu geben damit Composer schon bald die neue Contao Erweiterungsverwaltung werden kann.</p>';
$GLOBALS['TL_LANG']['composer_client']['migrate_mode']['0']             = 'Migrationsmodus';
$GLOBALS['TL_LANG']['composer_client']['migrate_mode']['1']             = 'Wir haben gesehen das Du %d Erweiterungen aus der alten Erweiterungsverwaltung nutzt. Nun wollen wir wissen, was wir mit diesen tun sollen.';
$GLOBALS['TL_LANG']['composer_client']['migrate_none']['0']             = 'Nichts tun (only for experts!)';
$GLOBALS['TL_LANG']['composer_client']['migrate_none']['1']             = 'Tue nichts, belasse alles dort, wo es ist. Das kann Probleme verursachen, wähle dies also nur, wenn Du weisst was Du tust!';
$GLOBALS['TL_LANG']['composer_client']['migrate_preconditions']         = '
<h2>Voraussetzungen</h2>
<ul class="preconditions">
<li class="{if smhEnabled==true}fehlgeschlagen{else}bestanden{endif}">SafeModeHack is {if smhEnabled==true}aktiviert{else}deaktiviert{endif}</li>
<li class="{if allowUrlFopenEnabled==true}bestanden{else}fehlgeschlagen{endif}">allow_url_fopen is {if allowUrlFopenEnabled==true}aktiviert{else}deaktiviert{endif}</li>
<li class="{if pharSupportEnabled==true}bestanden{else}fehlgeschlagen{endif}">PHAR Unterstützung ist {if pharSupportEnabled==true}aktiviert{else}deaktiviert{endif}</li>
<li class="{if composerSupported==true}bestanden{else}fehlgeschlagen{endif}">{if composerSupported==true}Du kannst die Composer Paketverwaltung benutzen :-){else}Du kannst die Composer Paketverwaltung nicht benutzen :-({endif}</li>
{if commercialPackages!==false}<li class="fail">Du benutzt kommerzielle Erweiterungen: ##commercialPackages##.<br>Bei einer Migration gehen diese verloren.<br>Bitte erkundige dich beim Entwickler, wenn dieser Composer unterstützt kannst Du ohne Bedenken fortfahren.</li>{endif}
<li class="{if apcOpcodeCacheEnabled==true}Warnung{else}bestanden{endif}">APC opcode cache ist {if apcOpcodeCacheEnabled==true}aktiviert, dies könnte ein unerwartetes Verhalten hervorrufen. If you have unexpected "cannot redeclare class" errors, try to disable APC opcode cache{elseif apcDisabledByUs==true}temporary disabled by Composer client{else}deaktiviert{endif}.</li>
</ul>';
$GLOBALS['TL_LANG']['composer_client']['migrate_production']['0']       = 'Für den produktiven Einsatz';
$GLOBALS['TL_LANG']['composer_client']['migrate_production']['1']       = 'Pakete werden als Archive heruntergeladen (nur zip Support ist Vorausgesetzt). Dateien werden als Kopie installiert.';
$GLOBALS['TL_LANG']['composer_client']['migrate_setup']['0']            = 'Konfigurationseinstellungen';
$GLOBALS['TL_LANG']['composer_client']['migrate_setup']['1']            = 'Bitte wählen Sie aus, welches Setup Sie für Ihre Installation benutzen wollen.';
$GLOBALS['TL_LANG']['composer_client']['migrate_setup_pre']             = '
<h2>Migrationsassistent</h2>
<p>Bevor wir mit der Migration starten, haben wir noch ein paar Fragen an dich.</p>';
$GLOBALS['TL_LANG']['composer_client']['migrate_skip']                  = 'Migration überspringen (Nur wenn du weißt was du tust)';
$GLOBALS['TL_LANG']['composer_client']['migrate_skip_confirm']          = 'Die Migration zu überspringen kann gefährlich sein, überspringe sie daher nur wenn du weißt was du tust. Migration jetzt überspringen?';
$GLOBALS['TL_LANG']['composer_client']['migrate_upgrade']['0']          = 'Erweiterungen nach Composer migrieren';
$GLOBALS['TL_LANG']['composer_client']['migrate_upgrade']['1']          = 'Die existierenden Pakete werden zum Composer Paketmanager hinzugefügt und neu installiert.<br>';
$GLOBALS['TL_LANG']['composer_client']['migrationDone']                 = 'Die Migration ist erfolgreich beendet worden.';
$GLOBALS['TL_LANG']['composer_client']['migrationSkipped']              = 'Die Migration wurde übersprungen.';
$GLOBALS['TL_LANG']['composer_client']['noInstallationCandidates']      = 'Kein Treffer für <em>%s</em> gefunden!';
$GLOBALS['TL_LANG']['composer_client']['noSearchResult']                = 'Keine Pakete gefunden für <em>%s</em>!';
$GLOBALS['TL_LANG']['composer_client']['no_conflicts']                  = 'keine Konflikte';
$GLOBALS['TL_LANG']['composer_client']['no_provides']                   = 'keine Bereitstellung';
$GLOBALS['TL_LANG']['composer_client']['no_releasedate']                = '-';
$GLOBALS['TL_LANG']['composer_client']['no_replaces']                   = 'keine Ersetzungen';
$GLOBALS['TL_LANG']['composer_client']['no_requires']                   = 'keine Abhängigkeiten';
$GLOBALS['TL_LANG']['composer_client']['no_suggests']                   = 'keine Empfehlungen';
$GLOBALS['TL_LANG']['composer_client']['not_installed']                 = 'Installation angefordert';
$GLOBALS['TL_LANG']['composer_client']['package_authors']               = 'Entwickler';
$GLOBALS['TL_LANG']['composer_client']['package_conflicts']             = 'Konflikte';
$GLOBALS['TL_LANG']['composer_client']['package_dependend_version']     = 'Abhängige Version';
$GLOBALS['TL_LANG']['composer_client']['package_homepage']              = 'Website';
$GLOBALS['TL_LANG']['composer_client']['package_installed_version']     = 'Installierte Version';
$GLOBALS['TL_LANG']['composer_client']['package_keywords']              = 'Keywords';
$GLOBALS['TL_LANG']['composer_client']['package_name']                  = 'Paket';
$GLOBALS['TL_LANG']['composer_client']['package_provides']              = 'Bereitstellung';
$GLOBALS['TL_LANG']['composer_client']['package_reference']             = 'Referenz';
$GLOBALS['TL_LANG']['composer_client']['package_replaces']              = 'Ersetzungen';
$GLOBALS['TL_LANG']['composer_client']['package_requested_version']     = 'Angeforderte Version';
$GLOBALS['TL_LANG']['composer_client']['package_requires']              = 'Abhängigkeiten';
$GLOBALS['TL_LANG']['composer_client']['package_source']                = 'Source';
$GLOBALS['TL_LANG']['composer_client']['package_suggests']              = 'Empfehlungen';
$GLOBALS['TL_LANG']['composer_client']['package_support']               = 'Support';
$GLOBALS['TL_LANG']['composer_client']['package_support_email']         = 'E-Mail';
$GLOBALS['TL_LANG']['composer_client']['package_support_irc']           = 'IRC-Chat';
$GLOBALS['TL_LANG']['composer_client']['package_support_issues']        = 'Tickets';
$GLOBALS['TL_LANG']['composer_client']['package_support_source']        = 'Source';
$GLOBALS['TL_LANG']['composer_client']['package_support_wiki']          = 'Wiki';
$GLOBALS['TL_LANG']['composer_client']['package_type']                  = 'Typ';
$GLOBALS['TL_LANG']['composer_client']['package_version']               = 'Version';
$GLOBALS['TL_LANG']['composer_client']['php_version']                   = 'PHP Version <strong>PHP %1$s</strong> oder neuer wird vorrausgesetzt. Deine Installation läuft mit PHP Version <strong>%2$s</strong>.<br>Bitte aktualisiere deine PHP Version.';
$GLOBALS['TL_LANG']['composer_client']['pinPackage']                    = 'Auf Version verankern';
$GLOBALS['TL_LANG']['composer_client']['pluginNotFound']                = 'Contao Composer Plugin wurde nicht gefunden!';
$GLOBALS['TL_LANG']['composer_client']['removeCandidate']               = 'Paket %s wurde gelöscht. Bitte aktualisieren Sie die Pakete um die Änderung zu übernehmen.';
$GLOBALS['TL_LANG']['composer_client']['removePackage']                 = 'Paket entfernen';
$GLOBALS['TL_LANG']['composer_client']['removePackages']                = 'ausgewählte Pakete entfernen';
$GLOBALS['TL_LANG']['composer_client']['resyncFailed']                  = 'Die erneute Synchronisierung des Paketes %s wurde mit der folgenden Meldung abgebrochen: %s';
$GLOBALS['TL_LANG']['composer_client']['resyncPackage']                 = 'Folgendes Paket wird erneut synchronisiert: %s';
$GLOBALS['TL_LANG']['composer_client']['resyncedPackage']               = 'Das Packet %s wurde erfolgreich erneut synchronisiert.';
$GLOBALS['TL_LANG']['composer_client']['save']                          = 'Speichern';
$GLOBALS['TL_LANG']['composer_client']['search']                        = 'Suchen';
$GLOBALS['TL_LANG']['composer_client']['search_headline']               = 'Suchergebnisse';
$GLOBALS['TL_LANG']['composer_client']['search_placeholder']            = 'Paketname oder Keyword';
$GLOBALS['TL_LANG']['composer_client']['settings_dialog']               = 'Einstellungen';
$GLOBALS['TL_LANG']['composer_client']['show_dependants']               = 'Zeige abhängige Pakete';
$GLOBALS['TL_LANG']['composer_client']['show_dependencies']             = '%d Abhängigkeiten installiert';
$GLOBALS['TL_LANG']['composer_client']['show_dependency_graph']         = 'Abhängigkeitsgraph';
$GLOBALS['TL_LANG']['composer_client']['solve_headline']                = 'Abhängigkeiten';
$GLOBALS['TL_LANG']['composer_client']['stability_alpha']               = 'Alphaversion';
$GLOBALS['TL_LANG']['composer_client']['stability_beta']                = 'Betaversion';
$GLOBALS['TL_LANG']['composer_client']['stability_dev']                 = 'Entwicklerversion';
$GLOBALS['TL_LANG']['composer_client']['stability_rc']                  = 'Release-Kandidat';
$GLOBALS['TL_LANG']['composer_client']['stability_stable']              = 'Stabil';
$GLOBALS['TL_LANG']['composer_client']['suhosin_enabled']               = 'Suhosin ist aktiviert.<br> Suhosin verhindert den Einsatz von Phar, bitte <a href="http://www.hardened-php.net/suhosin/" target="_blank">Suhosin</a> deaktivieren.';
$GLOBALS['TL_LANG']['composer_client']['terminate']                     = 'Beenden';
$GLOBALS['TL_LANG']['composer_client']['toBeRemoved']                   = 'wird entfernt';
$GLOBALS['TL_LANG']['composer_client']['tools_dialog']                  = 'Werkzeuge';
$GLOBALS['TL_LANG']['composer_client']['tools_resync']['0']             = 'erneute Synchronisierung';
$GLOBALS['TL_LANG']['composer_client']['tools_resync']['1']             = 'Alle durch Composer erstellten Ordnerkopien oder Symlinks werden erneut angelegt.';
$GLOBALS['TL_LANG']['composer_client']['tools_resync']['2']             = 'erneute Synchronisierung jetzt durchführen';
$GLOBALS['TL_LANG']['composer_client']['unknown_license']               = 'Unbekannte Lizenz';
$GLOBALS['TL_LANG']['composer_client']['unpinPackage']                  = 'Verankerung auf Version aufheben.';
$GLOBALS['TL_LANG']['composer_client']['unpinablePackage']              = 'Development Release können nicht verankert werden.';
$GLOBALS['TL_LANG']['composer_client']['update']                        = 'Pakete aktualisieren';
$GLOBALS['TL_LANG']['composer_client']['update_composer']               = 'Composer aktualisieren';
$GLOBALS['TL_LANG']['composer_client']['update_database']               = 'Datenbank aktualisieren';
$GLOBALS['TL_LANG']['composer_client']['vcs_requirements']              = '
<ul class="preconditions">
<li class="{if gitAvailable==true}pass{else}fail{endif}">
	git ist {if gitAvailable==true}verfügbar{else}nicht verfügbar, die meisten Pakete können nicht installiert werden!{endif}
</li>
<li class="{if hgAvailable==true}pass{else}fail{endif}">
	mercurial ist {if hgAvailable==true}verfügbar{else}nicht verfügbar, manche Pakete können nicht installiert werden!{endif}
</li>
<li class="{if svnAvailable==true}pass{else}fail{endif}">
	svn ist {if svnAvailable==true}verfügbar{else}nicht verfügbar, manche Pakete können nicht installiert werden!{endif}
</li>
</ul>';
$GLOBALS['TL_LANG']['composer_client']['version_bugfix']                = 'Bugfix Release %s (%s)';
$GLOBALS['TL_LANG']['composer_client']['version_exact']                 = 'exakte Version %s';
$GLOBALS['TL_LANG']['composer_client']['version_feature']               = 'Feature Release %s (%s)';
$GLOBALS['TL_LANG']['composer_client']['version_micro']                 = 'Mini-Version %s (%s)';
$GLOBALS['TL_LANG']['composer_client']['version_upstream']              = 'Upstream Release von %s (%s)';
$GLOBALS['TL_LANG']['composer_client']['widget_discard_changes']['0']   = 'Änderungen verwerfen';
$GLOBALS['TL_LANG']['composer_client']['widget_discard_changes']['1']   = 'Wählen Sie, wie Composer mit lokalen Änderungen umgehen soll (nur bei Installationsart "Quellen" relevant).';
$GLOBALS['TL_LANG']['composer_client']['widget_github_oauth']['0']      = 'Github oAuth Token';
$GLOBALS['TL_LANG']['composer_client']['widget_github_oauth']['1']      = 'Wenn Sie Probleme mit "api limit reached" in Verbindung mit Github, dann tragen Sie hier Ihr Github oAuth Token ein.';
$GLOBALS['TL_LANG']['composer_client']['widget_minimum_stability']['0'] = 'Minimale Stabilität';
$GLOBALS['TL_LANG']['composer_client']['widget_minimum_stability']['1'] = 'Die kleinste Stabilität setzt die minimal erlaubte Version zur Installation.';
$GLOBALS['TL_LANG']['composer_client']['widget_prefer_stable']['0']     = 'Stabil bevorzugen';
$GLOBALS['TL_LANG']['composer_client']['widget_prefer_stable']['1']     = 'Wenn möglich, bevorzuge stabile Pakete auch wenn die kleinste angeforderte Stabilität kleiner ist als ein stabiles Release.';
$GLOBALS['TL_LANG']['composer_client']['widget_preferred_install']['0'] = 'Bevorzugte Installationsart';
$GLOBALS['TL_LANG']['composer_client']['widget_preferred_install']['1'] = 'Wählen Sie bitte ob Sie die Paketsourcen (benötigt Git, Mercurial oder SVN) bevorzugen oder die Archive (funktioniert immer).';

