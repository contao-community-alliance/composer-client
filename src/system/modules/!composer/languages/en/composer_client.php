<?php

/**
 * Headlines
 */
$GLOBALS['TL_LANG']['composer_client']['errors_headline']           = 'System requirements';
$GLOBALS['TL_LANG']['composer_client']['composer_install_headline'] = 'Composer installation';
$GLOBALS['TL_LANG']['composer_client']['installed_headline']        = 'Installed packages';
$GLOBALS['TL_LANG']['composer_client']['search_headline']           = 'Search results';
$GLOBALS['TL_LANG']['composer_client']['install_headline']          = 'Install package';
$GLOBALS['TL_LANG']['composer_client']['solve_headline']            = 'Dependencies';
$GLOBALS['TL_LANG']['composer_client']['editor_headline']           = 'Experts mode';
$GLOBALS['TL_LANG']['composer_client']['dependency_graph_headline'] = 'Dependency graph';

/**
 * References
 */
$GLOBALS['TL_LANG']['composer_client']['ftp_mode']           = 'Safe-Mode-Hack is not supported.<br>Configure your hosting and run Contao without Safe-Mode-Hack.<br>&rarr; <a href="http://de.contaowiki.org/Safemode_Hack" target="_blank">Article about Safe-Mode-Hack in the Contao Wiki (german)</a>';
$GLOBALS['TL_LANG']['composer_client']['php_version']        = 'PHP version <strong>PHP 5.3.4</strong> or newer is required. Your system runs with PHP version <strong>%s</strong>.<br>Please upgrade your PHP.';
$GLOBALS['TL_LANG']['composer_client']['curl_missing']       = 'Curl is required to download packages.<br>Please install/enable the PHP <a href="http://php.net/curl" target="_blank">curl</a> module.';
$GLOBALS['TL_LANG']['composer_client']['composer_missing']   = 'Composer library is not completely installed.<br>Click <strong>Install Composer</strong> to install composer and its dependencies.';
$GLOBALS['TL_LANG']['composer_client']['install_composer']   = 'Install Composer';
$GLOBALS['TL_LANG']['composer_client']['search_placeholder'] = 'Package name or keyword';

/**
 * Settings dialog
 */
$GLOBALS['TL_LANG']['composer_client']['widget_minimum_stability'] = array(
	'Minimum stability',
	'The minimum stability set the lowest stability allowed to be installed.'
);
$GLOBALS['TL_LANG']['composer_client']['widget_prefer_stable']     = array(
	'Prefer stable',
	'If possible, prefer stable packages even if minimum stability is lower than stable.'
);
$GLOBALS['TL_LANG']['composer_client']['widget_preferred_install'] = array(
	'Preferred install',
	'Choose if you prefer source packages (require git, mercurial or svn) or dist archives (works every time).'
);

/**
 * Package listing
 */
$GLOBALS['TL_LANG']['composer_client']['package_name']              = 'Package';
$GLOBALS['TL_LANG']['composer_client']['package_version']           = 'Version';
$GLOBALS['TL_LANG']['composer_client']['package_requested_version'] = 'Requested version';
$GLOBALS['TL_LANG']['composer_client']['package_dependend_version'] = 'Dependent version';
$GLOBALS['TL_LANG']['composer_client']['package_installed_version'] = 'Installed version';
$GLOBALS['TL_LANG']['composer_client']['package_keywords']          = 'Keywords';
$GLOBALS['TL_LANG']['composer_client']['package_reference']         = 'Reference';
$GLOBALS['TL_LANG']['composer_client']['package_type']              = 'Type';
$GLOBALS['TL_LANG']['composer_client']['package_support']           = 'Support';
$GLOBALS['TL_LANG']['composer_client']['package_support_email']     = 'E-Mail';
$GLOBALS['TL_LANG']['composer_client']['package_support_issues']    = 'Issues';
$GLOBALS['TL_LANG']['composer_client']['package_support_wiki']      = 'Wiki';
$GLOBALS['TL_LANG']['composer_client']['package_support_irc']       = 'IRC chat';
$GLOBALS['TL_LANG']['composer_client']['package_support_source']    = 'Source';
$GLOBALS['TL_LANG']['composer_client']['package_source']            = 'Source';
$GLOBALS['TL_LANG']['composer_client']['package_authors']           = 'Developer';
$GLOBALS['TL_LANG']['composer_client']['package_homepage']          = 'Homepage';
$GLOBALS['TL_LANG']['composer_client']['package_requires']          = 'Dependencies';
$GLOBALS['TL_LANG']['composer_client']['package_suggests']          = 'Suggestions';
$GLOBALS['TL_LANG']['composer_client']['package_provides']          = 'Provides';
$GLOBALS['TL_LANG']['composer_client']['package_conflicts']         = 'Conflicts';
$GLOBALS['TL_LANG']['composer_client']['package_replaces']          = 'Replaces';
$GLOBALS['TL_LANG']['composer_client']['no_requires']               = 'no dependencies';
$GLOBALS['TL_LANG']['composer_client']['no_suggests']               = 'no suggestions';
$GLOBALS['TL_LANG']['composer_client']['no_provides']               = 'no provides';
$GLOBALS['TL_LANG']['composer_client']['no_conflicts']              = 'no conflicts';
$GLOBALS['TL_LANG']['composer_client']['no_replaces']               = 'no replaces';
$GLOBALS['TL_LANG']['composer_client']['not_installed']             = 'Installation requested';
$GLOBALS['TL_LANG']['composer_client']['install_via']               = 'by %s: %s';
$GLOBALS['TL_LANG']['composer_client']['dependency_of']             = 'Dependency of %s';
$GLOBALS['TL_LANG']['composer_client']['installed_in']              = 'installed in version %s';
$GLOBALS['TL_LANG']['composer_client']['no_releasedate']            = '-';
$GLOBALS['TL_LANG']['composer_client']['show_dependencies']         = '%d dependencies installed';
$GLOBALS['TL_LANG']['composer_client']['show_dependency_graph']     = 'Dependency graph';

/**
 * Versions
 */
$GLOBALS['TL_LANG']['composer_client']['version_exact']    = 'exact version %s';
$GLOBALS['TL_LANG']['composer_client']['version_micro']    = 'Micro releases %s (%s)';
$GLOBALS['TL_LANG']['composer_client']['version_bugfix']   = 'Bugfix releases %s (%s)';
$GLOBALS['TL_LANG']['composer_client']['version_feature']  = 'Feature releases %s (%s)';
$GLOBALS['TL_LANG']['composer_client']['version_upstream'] = 'Upstream releases from %s (%s)';

/**
 * Stabilities
 */
$GLOBALS['TL_LANG']['composer_client']['stability_stable'] = 'Stable';
$GLOBALS['TL_LANG']['composer_client']['stability_rc']     = 'Release candidate';
$GLOBALS['TL_LANG']['composer_client']['stability_beta']   = 'Beta release';
$GLOBALS['TL_LANG']['composer_client']['stability_alpha']  = 'Alpha release';
$GLOBALS['TL_LANG']['composer_client']['stability_dev']    = 'Development release';

/**
 * Install source
 */
$GLOBALS['TL_LANG']['composer_client']['install_source'] = 'Sources';
$GLOBALS['TL_LANG']['composer_client']['install_dist']   = 'Dist archive';
$GLOBALS['TL_LANG']['composer_client']['install_auto']   = 'Auto';

/**
 * Message
 */
$GLOBALS['TL_LANG']['composer_client']['composerUpdateRequired']   = 'Composer version is older than 30 days, please update composer.';
$GLOBALS['TL_LANG']['composer_client']['composerUpdated']          = 'Composer was updated!';
$GLOBALS['TL_LANG']['composer_client']['noSearchResult']           = 'No packages found for <em>%s</em>!';
$GLOBALS['TL_LANG']['composer_client']['noInstallationCandidates'] = 'No candicate found for <em>%s</em>!';
$GLOBALS['TL_LANG']['composer_client']['unknown_license']          = 'unknown license';
$GLOBALS['TL_LANG']['composer_client']['added_candidate']          = 'Package %s added in version %s. Update packages to apply changes.';
$GLOBALS['TL_LANG']['composer_client']['removeCandidate']          = 'Package %s removed. Update packages to apply changes.';
$GLOBALS['TL_LANG']['composer_client']['configValid']              = 'The configuration is valid.';
$GLOBALS['TL_LANG']['composer_client']['removePackage']            = 'Remove package';
$GLOBALS['TL_LANG']['composer_client']['confirmRemove']            = 'Are you sure to remove the package %s?';
$GLOBALS['TL_LANG']['composer_client']['toBeRemoved']              = 'to be removed';
$GLOBALS['TL_LANG']['composer_client']['databaseUpdated']          = 'Database updated, %d queries executed.';
$GLOBALS['TL_LANG']['composer_client']['databaseUptodate']         = 'Database is up to date.';
$GLOBALS['TL_LANG']['composer_client']['composerCacheCleared']     = 'Composer cache cleared.';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['composer_client']['update_database']      = 'Update database';
$GLOBALS['TL_LANG']['composer_client']['settings_dialog']      = 'Settings';
$GLOBALS['TL_LANG']['composer_client']['experts_mode']         = 'Experts mode';
$GLOBALS['TL_LANG']['composer_client']['clear_composer_cache'] = 'clear Composer cache';
$GLOBALS['TL_LANG']['composer_client']['update_composer']      = 'Update composer';
$GLOBALS['TL_LANG']['composer_client']['search']               = 'Search';
$GLOBALS['TL_LANG']['composer_client']['check']                = 'Check compatibility';
$GLOBALS['TL_LANG']['composer_client']['mark_to_install']      = 'Mark package to install';
$GLOBALS['TL_LANG']['composer_client']['mark_and_install']     = 'Install package now';
$GLOBALS['TL_LANG']['composer_client']['update']               = 'Update packages';
$GLOBALS['TL_LANG']['composer_client']['save']                 = 'Save';
