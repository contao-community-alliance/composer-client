CHANGELOG
=========

This changelog references the relevant changes (bug and security fixes and new features and improvements) done.

Version 0.12.3
--------------

* Bugfix: Fix issue with empty database update in the install tool.

Version 0.12.2
--------------

* Bugfix: Fix issue with invalid argument type "string" in the details view.

Version 0.12.1
--------------

* Bugfix: Access to assets in the composer/vendor/ directory is now granted.
* Bugfix: non-Composer packages will not be marked as incompatible anymore.

Version 0.12
------------

* Improvement: Some little performance optimisations.
* Improvement: Its possible to disable the "auto update composer.phar" feature.
* Improvement: Show type and description in the search result.
* Improvement: Show a warning on incompatible packages in the search result.
* Improvement: Show a warning on incompatible package versions in the details view.
* Bugfix: Do not show the multi-remove checkbox on dependencies.

Version 0.11
------------

* Improvement: Add tool to resync all shadow copies and symlinks.

Version 0.10.2
--------------

* Improvement: It's now possible to remove multiple packages at once.
* Bugfix: It's not possible to remove undeletable packages (e.g. contao/core) anymore.
* Bugfix: It's not possible to change the version of unmodifiable packages (e.g. contao/core) anymore.

Version 0.10.1
--------------

* Bugfix: Fix the >> package "" listed for update is not installed << message.

Version 0.10
------------

* Feature: It is now possible to run the update in dry-run mode.
* Feature: It is now possible to update only a selected set of packages.

Version 0.9.2
-------------

* Bugfix: Fix "null is not an array" issue with database update tool when the tl_repository_* tables are removed.

Version 0.9.1
-------------

* Improvement: The repository client database tables will not be dropped by default, until you enable the option in the system settings.
* Improvement: The repository client will not be disabled, if the composer client is not supported.
* Improvement: On config update, the package update operation will not automatically restarted.
* Improvement: If you install the composer client, you will automatically redirected to the composer client.
* Improvement: When migrate legacy packages, the constraint is more tollerant. This solve update issues with unresolveable conclusions.
* Bugfix: Do not use the vendor path for icons. The icons will now be used when composer client is installed from ER2.

Version 0.9.0
-------------

* Improvement: Switch from the old contao-community-alliance/composer-installer to the contao-community-alliance/composer-plugin.

Version 0.8.8
-------------

* Bugfix: Prevent exception if the APC extension is not loaded.

Version 0.8.7
-------------

* Bugfix: Fix the generated constraint for feature releases.

Version 0.8.6
-------------

* Bugfix: Fix the check, if APC is enabled for APC >= 3.0.13.

Version 0.8.5
-------------

* Bugfix: Disable the hooks only while updating packages.

Version 0.8.4
-------------

* Bugfix: Ignore suhosin, if PHAR files are whitelisted.

Version 0.8.3
-------------

* Improvement: Optimize the performance of the solver dialog.
* Improvement: Initialize the composer.json with a stable constraint for the composer client.
* Bugfix: Fix #124, check if the repository client table exist, when migrade old ER2 packages.

Version 0.8.2
-------------

* Bugfix: #112, disable all TL_HOOKS, while in the composer client backend.

Version 0.8.1
-------------

* Bugfix: Re-run all runonces when the contao version has changes.

Version 0.8
-----------

* Feature: #115, it is now possible to pin packages to a specific version.
* Bugfix: #117, fix issue with internal cache.
* Bugfix: #111, fix removing packages via UI now works again.
* Internal: The backend views are now split into controllers. Thanks to backbone97 for this inspiration.

Version 0.7.14
--------------

* Bugfix: Fix that APC is suggested as enabled (even if it is disabled), when the ini_set function is disabled.

Version 0.7.13
--------------

* Bugfix: Detect APCU correctly and not complain that APC - which not exists - is enabled.

Version 0.7.12
--------------

* Improvement: The generated constraints in version selection will now use *-dev as upper border. This prevent installing of next-major dev packages.

Version 0.7.11
--------------

* Improvement: The replacement information in the package list is now better placed.
* Bugfix: Remove packages where a successor/replacement package is installed is now possible.
* Bugfix: #105, long description lines are now wrapped in the search listing and will not break layout anymore.
* Bugfix: Fix that the (not installed) required package and the (installed) replacement packages are shown in the package list.

Version 0.7.10
--------------

* Improvement: Update the visual of the #tl_buttons bar according to new contao 3 layout.
* Improvement: Show confirmation messages instead of error messages, when the installer update the composer config.
* Bugfix: Fix that the remove button is shown for dependencies in the package list.

Version 0.7.9
-------------

* Bugfix: Fix endless recursion issue with replacement packages.

Version 0.7.6
-------------

* Bugfix: Display the correct require constraint for replacement packages.

Version 0.7.5
-------------

* Improvement: In package list hide packages that are replaced by a successor and show which package replace another.

Version 0.7.3
-------------

* Improvement: Add a new method to hack the contao 2 classes cache.

Version 0.7.2
-------------

* Bugfix: Keep the files of the ER2 "composer" package on migration.
* Bugfix: Fix some missing global namespace prefixes.

Version 0.7.1
-------------

* Bugfix: Skip the ER2 "composer" package on migration.

Version 0.7.0
-------------

* Feature: Check for suhosin and show an compatibility error that suhosin is not supported.
* Internal: Rework the classes into namespaces and introduce a custom minimalistic class loader.
* Internal: Rework the internal classes and introduce a Runtime class that hold a lot of convenience and runtime related methods.
