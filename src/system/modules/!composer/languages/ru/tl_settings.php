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
 * last-updated: 2014-05-21T23:36:46+02:00
 */

$GLOBALS['TL_LANG']['tl_settings']['composerExecutionMode']['0']                             = 'Режим выполнения';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionMode']['1']                             = 'Выберите, как должен выполняться бинарный файл Composer.';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['detached']['0']                = 'как автономный процесс';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['detached']['1']                = 'Composer будет исполняться как обособленный процесс в фоновом режиме. Это невозможно или не разрешено на некоторых системах (уточните у провайдера о возможности запуска фоновых процессов). Этот метод практически не имеет ограничений.';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['inline']['0']                  = 'в HTTP-запросе';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['inline']['1']                  = 'Composer будет выполняться в рамках процесса веб-сервера. Этот режим обычно медленнее и работает на всех системах, но подвергается максимальным ограничениям по времени выполнения PHP.';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['process']['0']                 = 'как подпроцесс в процессе веб-сервера';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['process']['1']                 = 'Composer будет выполняться через вызов подпроцесса как внешняя программа. Это обычно быстрее и работа возможна в некоторых системах, но подвергается максимальным ограничениям по времени выполнения PHP.';
$GLOBALS['TL_LANG']['tl_settings']['composerPhpPath']['0']                                   = 'PHP путь/команда';
$GLOBALS['TL_LANG']['tl_settings']['composerPhpPath']['1']                                   = 'Путь или команда к бинарному файлу php.';
$GLOBALS['TL_LANG']['tl_settings']['composerRemoveRepositoryTables']['0']                    = 'Удалить таблицы клиента хранилища расширений';
$GLOBALS['TL_LANG']['tl_settings']['composerRemoveRepositoryTables']['1']                    = 'Таблицы старого клиента хранилища расширений не будут удалены Composer, до тех пор, пока вы не выберите этот чек-бокс.';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosity']['0']                                 = 'Детализация в журнале';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosity']['1']                                 = 'Консоль имеет 5 уровней детализации, если у вас нет проблем, оставьте уровень по умолчанию, ';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_DEBUG']['0']        = 'Вывод отладочных сообщений';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_DEBUG']['1']        = 'В журнал выводятся все сообщения, включая сообщения отладки, которые скорее всего не используются обычными пользователями.';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_NORMAL']['0']       = 'Детализация по умолчанию';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_NORMAL']['1']       = 'Уровень детализации по умолчанию, используйте, если у вас нет проблем.';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_QUIET']['0']        = 'Бесшумный режим';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_QUIET']['1']        = 'Не выводить никаких сообщений в журнале.';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_VERBOSE']['0']      = 'Подробная детализация';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_VERBOSE']['1']      = 'Увеличение детализации сообщений в журнале.';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_VERY_VERBOSE']['0'] = 'Очень подробная детализация';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_VERY_VERBOSE']['1'] = 'Включить в журнал не важные информационные сообщения.';
$GLOBALS['TL_LANG']['tl_settings']['composer_legend']                                        = 'Настройки Composer';

