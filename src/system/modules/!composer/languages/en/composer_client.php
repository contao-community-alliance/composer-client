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
$GLOBALS['TL_LANG']['composer_client']['detached']                  = 'Package update';

/**
 * References
 */
$GLOBALS['TL_LANG']['composer_client']['ftp_mode']              =
    'Safe-Mode-Hack is not supported.<br>' .
    'Configure your hosting and run Contao without Safe-Mode-Hack.<br>' .
    '&rarr; <a href="http://de.contaowiki.org/Safemode_Hack" target="_blank">Article about Safe-Mode-Hack' .
    ' in the Contao Wiki (German)</a>';
$GLOBALS['TL_LANG']['composer_client']['php_version']           =
    'PHP version <strong>PHP %1$s</strong> or newer is required. ' .
    'Your system runs with PHP version <strong>%2$s</strong>.<br>Please upgrade your PHP.';
$GLOBALS['TL_LANG']['composer_client']['curl_missing']          =
    'cURL is required to download packages.<br>' .
    'Please install/enable the PHP <a href="http://php.net/curl" target="_blank">curl</a> module.';
$GLOBALS['TL_LANG']['composer_client']['could_not_disable_apc'] =
    'Could not disable APC.<br>' .
    'APC and Composer produce random errors, please disable <a href="http://php.net/apc" target="_blank">APC</a>.';
$GLOBALS['TL_LANG']['composer_client']['suhosin_enabled']       =
    'Suhosin is enabled.<br>' .
    'Suhosin will break the Phar support, please disable ' .
    '<a href="http://www.hardened-php.net/suhosin/" target="_blank">Suhosin</a>.';
$GLOBALS['TL_LANG']['composer_client']['composer_missing']      =
    'Composer library is not completely installed.<br>' .
    'Click <strong>Install Composer</strong> to install Composer and its dependencies.';
$GLOBALS['TL_LANG']['composer_client']['install_composer']      = 'Install Composer';
$GLOBALS['TL_LANG']['composer_client']['search_placeholder']    = 'Package name or keyword';
$GLOBALS['TL_LANG']['composer_client']['download_impossible']   =
    'Downloading is impossible, please either enable the PHP zip extension or ensure proc_open() may run unzip.';

/**
 * Migration wizard
 */
$GLOBALS['TL_LANG']['composer_client']['migrate_intro'] = '
<p>Dear user, this is the new Contao package manager, based on the PHP dependency manager
   <a href="http://getcomposer.org/" target="_blank">Composer</a>.</p>
<p>This is a public beta phase. We need your help to test this client until it will be the
   default Contao extension manager.</p>';

$GLOBALS['TL_LANG']['composer_client']['migrate_preconditions'] = '
<h2>Preconditions</h2>
<ul class="preconditions">
<li class="{if smhEnabled==true}fail{else}pass{endif}">
  SafeModeHack is {if smhEnabled==true}enabled{else}disabled{endif}
</li>
<li class="{if allowUrlFopenEnabled==true}pass{else}fail{endif}">
  allow_url_fopen is {if allowUrlFopenEnabled==true}enabled{else}disabled{endif}
</li>
<li class="{if pharSupportEnabled==true}pass{else}fail{endif}">
  PHAR support is {if pharSupportEnabled==true}enabled{else}disabled{endif}
</li>
<li class="{if composerSupported==true}pass{else}fail{endif}">
  {if composerSupported==true}You can use the Composer client :-){else}You cannot use the Composer client :-({endif}
</li>
{if commercialPackages!==false}
<li class="fail">
  You have installed some commercial extensions: ##commercialPackages##.<br>
  You may loose them on migration.<br>
  Please consult the publisher, if he support Composer you can continue without concern.
</li>
{endif}
<li class="{if apcOpcodeCacheEnabled==true}warn{else}pass{endif}">
  APC opcode cache is
  {if apcOpcodeCacheEnabled==true}enabled, this may produce unexpected exceptions.
  If you have unexpected "cannot redeclare class" errors, try to disable APC opcode cache
  {elseif apcDisabledByUs==true}
  temporary disabled by Composer client
  {else}
  disabled
  {endif}.</li>
</ul>';

$GLOBALS['TL_LANG']['composer_client']['migrate_faq'] = '
<h2>FAQ</h2>
<ul class="questions">
<li>
    <h3>It is necessary to use this client?</h3>
    Not at all, it is optional. But some developers distribute
    new features or new extensions only via this package manager.
    You may miss some essential updates if you not use it.
</li>
<li>
    <h3>Can I install packages, that are available in the current extension repository?</h3>
    Yes you can. All public packages are synchronized into the new
    repository (they are prefixed with <em>contao-legacy/</em>).<br>
    <em>Please note that existing commercial extensions cannot be
    installed with Composer due to license limitations.
    Please ask the publisher to support Composer.</em>
</li>
<li>
    <h3>Will there be a new extension repository?</h3>
    Yes, a new extension repository exists on <a href="http://legacy-packages-via.contao-community-alliance.org/"
      target="_blank">legacy-packages-via.contao-community-alliance.org</a>.
    Currently it is a plain packagist installation, but we will improve it shortly with all our needs.
</li>
<li>
    <h3>What is Composer and this Composer package manager?</h3>
    The answer is too long to be answered here. Read the article about the Composer Client in the
    <a href="http://de.contaowiki.org/Composer_Client" target="_blank">Contao Wiki</a>.
</li>
<li>
    <h3>Can I switch back to the old package manager?</h3>
    Yes you can. Go to the Composer client settings dialog and choose "switch back to old client".
</li>
<li>
    <h3>I have problems with the new client, where can I ask for help?</h3>
    This client is driven by the community.
    You can ask in the <a href="https://community.contao.org/de/forumdisplay.php?6-Entwickler-Fragen"
      target="_blank">community board</a>,
    the official IRC channel <a href="irc://chat.freenode.net/%23contao.composer">#contao.composer</a>
    or the <a href="https://github.com/contao-community-alliance/composer/issues" target="_blank">ticket system</a>.
</li>
</ul>';

$GLOBALS['TL_LANG']['composer_client']['migrate_setup_pre'] = '
<h2>Migration setup</h2>
<p>Before you start with the new client, we have to ask you some questions.</p>';

$GLOBALS['TL_LANG']['composer_client']['migrate_mode']         = array(
    'Migration mode',
    'We detected that you have installed %d extensions with the old package manager. ' .
    'Now we want to ask you, what should we do with the old packages?'
);
$GLOBALS['TL_LANG']['composer_client']['migrate_upgrade']      = array(
    'Upgrade packages to Composer',
    'Existing packages will be added to Composer package manager and reinstalled.<br>'
);
$GLOBALS['TL_LANG']['composer_client']['migrate_clean']        = array(
    'Remove packages',
    'Remove previously installed packages and start with a clean setup.'
);
$GLOBALS['TL_LANG']['composer_client']['migrate_none']         = array(
    'Do nothing (only for experts!)',
    'Do nothing, keep everything where it is. This can make problems, only choose this if you know what you do!'
);
$GLOBALS['TL_LANG']['composer_client']['migrate_setup']        = array(
    'Configuration setup',
    'Please choose for which setup this installation is used.'
);
$GLOBALS['TL_LANG']['composer_client']['migrate_production']   = array(
    'For production use',
    'Packages will be fetched as archives (only zip support is required). Files will be installed as copies.'
);
$GLOBALS['TL_LANG']['composer_client']['migrate_development']  = array(
    'For development use',
    'Packages will be fetched as sources with git, mercurial or svn. Files will be installed as symlinks.'
);
$GLOBALS['TL_LANG']['composer_client']['vcs_requirements']     = '
<ul class="preconditions">
<li class="{if gitAvailable==true}pass{else}fail{endif}">
    git is {if gitAvailable==true}available{else}missing, most packages may fail to install!{endif}
</li>
<li class="{if hgAvailable==true}pass{else}fail{endif}">
    mercurial is {if hgAvailable==true}available{else}missing, some packages may fail to install!{endif}
</li>
<li class="{if svnAvailable==true}pass{else}fail{endif}">
    svn is {if svnAvailable==true}available{else}missing, some packages may fail to install!{endif}
</li>
</ul>
';
$GLOBALS['TL_LANG']['composer_client']['migrate_do']           = 'do migration';
$GLOBALS['TL_LANG']['composer_client']['migrate_skip']         = 'skip migration (only if you know what you do)';
$GLOBALS['TL_LANG']['composer_client']['migrate_skip_confirm'] =
    'Skipping the migration may be dangerous, skip migration only if you know what you do. Skip migration now?';
$GLOBALS['TL_LANG']['composer_client']['undo_migration_text']  = '
<p>Dear user, we are sorry that you decide to switch back to the old packages client.</p>
<p>Some words about how the switch works:</p>
<ul>
<li>Before switching, all installed packages will be removed.</li>
<li>Migration status will be reset.</li>
<li>The Composer client will be disabled</li>
<li>The repository client will be enabled</li>
<li>You need to reinstall all extensions, that are managed by the repository client!</li>
</ul>
<br>
';
$GLOBALS['TL_LANG']['composer_client']['undo_migration']       = 'switch back to old client now';

/**
 * Tools dialog
 */
$GLOBALS['TL_LANG']['composer_client']['tools_resync'] = array(
    'Resync',
    'Resync all shadow copies and symlinks.',
    'Resync now'
);

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
$GLOBALS['TL_LANG']['composer_client']['widget_github_oauth']      = array(
    'github oauth Token',
    'If you have "api limit reached" problems with github, put a github oauth token here.'
);
$GLOBALS['TL_LANG']['composer_client']['widget_discard_changes']   = array(
    'Discard changes',
    'Choose how changes should be handled.'
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
$GLOBALS['TL_LANG']['composer_client']['package_support_email']     = 'Email';
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
$GLOBALS['TL_LANG']['composer_client']['show_dependants']           = 'Show dependent packages';
$GLOBALS['TL_LANG']['composer_client']['show_dependencies']         = '%d dependencies installed';
$GLOBALS['TL_LANG']['composer_client']['show_dependency_graph']     = 'Dependency graph';
$GLOBALS['TL_LANG']['composer_client']['dependency_recursion']      = '(circular dependency)';

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
 * Discard changes
 */
$GLOBALS['TL_LANG']['composer_client']['discard_changes_yes']   = 'Discard';
$GLOBALS['TL_LANG']['composer_client']['discard_changes_no']    = 'Keep (stop update)';
$GLOBALS['TL_LANG']['composer_client']['discard_changes_stash'] = 'Stash and reapply';

/**
 * Message
 */
$GLOBALS['TL_LANG']['composer_client']['migrationSkipped']         = 'Migration was skipped.';
$GLOBALS['TL_LANG']['composer_client']['migrationDone']            = 'Migration successfully finished.';
$GLOBALS['TL_LANG']['composer_client']['composerUpdateRequired']   =
    'Composer version is older than 30 days, please update Composer.';
$GLOBALS['TL_LANG']['composer_client']['composerUpdateNecessary']  =
    'You use an incompatible version of the Composer library. ' .
    'It is necessary to update the Composer library or the Composer client may not work as expected.';
$GLOBALS['TL_LANG']['composer_client']['composerUpdated']          = 'Composer was updated!';
$GLOBALS['TL_LANG']['composer_client']['noSearchResult']           = 'No packages found for <em>%s</em>!';
$GLOBALS['TL_LANG']['composer_client']['noInstallationCandidates'] = 'No candidate found for <em>%s</em>!';
$GLOBALS['TL_LANG']['composer_client']['incompatiblePackage']      = '(not compatible with this Contao version)';
$GLOBALS['TL_LANG']['composer_client']['incompatiblePackageLong']  =
    'This package version is not compatible with this Contao version!';
$GLOBALS['TL_LANG']['composer_client']['unknown_license']          = 'unknown license';
$GLOBALS['TL_LANG']['composer_client']['added_candidate']          =
    'Package %s added in version %s. Update packages to apply changes.';
$GLOBALS['TL_LANG']['composer_client']['removeCandidate']          =
    'Package %s removed. Update packages to apply changes.';
$GLOBALS['TL_LANG']['composer_client']['configValid']              = 'The configuration is valid.';
$GLOBALS['TL_LANG']['composer_client']['pinPackage']               = 'pin on version';
$GLOBALS['TL_LANG']['composer_client']['unpinablePackage']         = 'development releases cannot be pinned';
$GLOBALS['TL_LANG']['composer_client']['unpinPackage']             = 'release pin on version';
$GLOBALS['TL_LANG']['composer_client']['removePackage']            = 'Remove package';
$GLOBALS['TL_LANG']['composer_client']['confirmRemove']            = 'Are you sure to remove the package %s?';
$GLOBALS['TL_LANG']['composer_client']['removePackages']           = 'Remove';
$GLOBALS['TL_LANG']['composer_client']['confirmRemovePackages']    = 'Are you sure to remove these packages: %s?';
$GLOBALS['TL_LANG']['composer_client']['toBeRemoved']              = 'to be removed';
$GLOBALS['TL_LANG']['composer_client']['databaseUpdated']          = 'Database updated, %d queries executed.';
$GLOBALS['TL_LANG']['composer_client']['databaseUptodate']         = 'Database is up to date.';
$GLOBALS['TL_LANG']['composer_client']['composerCacheCleared']     = 'Composer cache cleared.';
$GLOBALS['TL_LANG']['composer_client']['pluginNotFound']           = 'Contao Composer Plugin not found!';
$GLOBALS['TL_LANG']['composer_client']['resyncPackage']            = 'Resynching package %s';
$GLOBALS['TL_LANG']['composer_client']['resyncedPackage']          = 'Package %s resynced successfully';
$GLOBALS['TL_LANG']['composer_client']['resyncFailed']             = 'Resyncing package %s failed with message: %s';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['composer_client']['migrate']              = 'Migrate';
$GLOBALS['TL_LANG']['composer_client']['update_database']      = 'Update database';
$GLOBALS['TL_LANG']['composer_client']['tools_dialog']         = 'Tools';
$GLOBALS['TL_LANG']['composer_client']['settings_dialog']      = 'Settings';
$GLOBALS['TL_LANG']['composer_client']['undo_migration']       = 'Switch back to old client';
$GLOBALS['TL_LANG']['composer_client']['clear_composer_cache'] = 'Clear Composer cache';
$GLOBALS['TL_LANG']['composer_client']['experts_mode']         = 'Experts mode';
$GLOBALS['TL_LANG']['composer_client']['update_composer']      = 'Update Composer';
$GLOBALS['TL_LANG']['composer_client']['search']               = 'Search';
$GLOBALS['TL_LANG']['composer_client']['check']                = 'Check compatibility';
$GLOBALS['TL_LANG']['composer_client']['mark_to_install']      = 'Mark package to install';
$GLOBALS['TL_LANG']['composer_client']['mark_and_install']     = 'Install package now';
$GLOBALS['TL_LANG']['composer_client']['dry-run']              = 'Dry run';
$GLOBALS['TL_LANG']['composer_client']['update']               = 'Update packages';
$GLOBALS['TL_LANG']['composer_client']['save']                 = 'Save';
$GLOBALS['TL_LANG']['composer_client']['close']                = 'Close';
$GLOBALS['TL_LANG']['composer_client']['terminate']            = 'Terminate';
