<?php
/**
 * Translations are managed using Transifex. To create a new translation
 * or to help to maintain an existing one, please register at transifex.com.
 *
 * @link http://help.transifex.com/intro/translating.html
 * @link https://www.transifex.com/projects/p/composer/language/ru/
 *
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 *
 * last-updated: 2015-07-07T20:18:54+02:00
 */

$GLOBALS['TL_LANG']['tl_settings']['composerAutoUpdateLibrary']['0']                         = 'Автоматическое обновление библиотеки Composer';
$GLOBALS['TL_LANG']['tl_settings']['composerAutoUpdateLibrary']['1']                         = 'Библиотека Composer (<code>composer.phar</code>) будет обновлена автоматически ​​каждые 30 дней.';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionMode']['0']                             = 'Режим выполнения';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionMode']['1']                             = 'Выберите, как должен выполняться бинарный файл Composer.';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['detached']['0']                = 'как автономный процесс';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['detached']['1']                = 'Composer будет исполняться как обособленный процесс в фоновом режиме. Это невозможно или не разрешено на некоторых системах (уточните у провайдера о возможности запуска фоновых процессов). Этот метод практически не имеет ограничений.';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['inline']['0']                  = 'в HTTP-запросе';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['inline']['1']                  = 'Composer будет выполняться в рамках процесса веб-сервера. Этот режим обычно медленнее и работает на всех системах, но подвергается максимальным ограничениям по времени выполнения PHP.';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['process']['0']                 = 'как подпроцесс в процессе веб-сервера';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['process']['1']                 = 'Composer будет выполняться через вызов подпроцесса как внешняя программа. Это обычно быстрее, но возможно только на системах, поддерживающих proc_open() и подвергается максимальным ограничениям по времени выполнения PHP.';
$GLOBALS['TL_LANG']['tl_settings']['composerPhpPath']['0']                                   = 'PHP путь/команда';
$GLOBALS['TL_LANG']['tl_settings']['composerPhpPath']['1']                                   = 'Путь или команда к бинарному файлу php.';
$GLOBALS['TL_LANG']['tl_settings']['composerProfiling']['0']                                 = 'Включить профилирование';
$GLOBALS['TL_LANG']['tl_settings']['composerProfiling']['1']                                 = 'Показать сведения об использовании времени и памяти.';
$GLOBALS['TL_LANG']['tl_settings']['composerRemoveRepositoryTables']['0']                    = 'Удалить таблицы клиента хранилища расширений';
$GLOBALS['TL_LANG']['tl_settings']['composerRemoveRepositoryTables']['1']                    = 'Таблицы старого клиента хранилища расширений не будут удалены Composer, до тех пор, пока вы не выберите этот чек-бокс.';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosity']['0']                                 = 'Детализация журнала консоли';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosity']['1']                                 = 'Консоль имеет 5 режимов вывода информации, если у вас нет проблем, оставьте значение по умолчанию.';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_DEBUG']['0']        = 'Вывод отладочных сообщений';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_DEBUG']['1']        = 'В журнал выводятся все сообщения, включая отладочные, которые чаще всего не используются обычными пользователями.';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_NORMAL']['0']       = 'Режим по умолчанию';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_NORMAL']['1']       = 'Используйте, если у вас нет проблем.';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_QUIET']['0']        = 'Тихий режим';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_QUIET']['1']        = 'Сообщения журнала консоли не выводятся.';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_VERBOSE']['0']      = 'Подробный режим';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_VERBOSE']['1']      = 'Увеличение детализации сообщений в журнале.';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_VERY_VERBOSE']['0'] = 'Очень подробный режим';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_VERY_VERBOSE']['1'] = 'Увеличение детализации сообщений в журнале, включая не важные информационные сообщения.';
$GLOBALS['TL_LANG']['tl_settings']['composer_legend']                                        = 'Настройки Composer';

