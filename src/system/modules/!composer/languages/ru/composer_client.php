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
 * last-updated: 2014-05-06T13:57:40+02:00
 */


$GLOBALS['TL_LANG']['composer_client']['check']                         = 'Проверить совместимость';
$GLOBALS['TL_LANG']['composer_client']['clear_composer_cache']          = 'Очистить кэш Composer';
$GLOBALS['TL_LANG']['composer_client']['close']                         = 'Закрыть';
$GLOBALS['TL_LANG']['composer_client']['composerCacheCleared']          = 'Кэш Composer очищен.';
$GLOBALS['TL_LANG']['composer_client']['composerUpdated']               = 'Composer был обновлен!';
$GLOBALS['TL_LANG']['composer_client']['composer_install_headline']     = 'Установка Composer';
$GLOBALS['TL_LANG']['composer_client']['composer_missing']              = 'Библиотека Composer установлена не полностью.<br>Нажмите <strong>Установить Composer</strong> для установки Composer и его зависимостей.';
$GLOBALS['TL_LANG']['composer_client']['databaseUpdated']               = 'База данных обновлена, выполнено запросов: %d.';
$GLOBALS['TL_LANG']['composer_client']['databaseUptodate']              = 'База данных обновлена.';
$GLOBALS['TL_LANG']['composer_client']['dependency_graph_headline']     = 'Схема зависимостей';
$GLOBALS['TL_LANG']['composer_client']['dependency_recursion']          = '(циклическая зависимость)';
$GLOBALS['TL_LANG']['composer_client']['experts_mode']                  = 'Режим эксперта';
$GLOBALS['TL_LANG']['composer_client']['install_auto']                  = 'Автоматически';
$GLOBALS['TL_LANG']['composer_client']['install_composer']              = 'Установить Composer';
$GLOBALS['TL_LANG']['composer_client']['install_dist']                  = 'Дистрибутивный архив';
$GLOBALS['TL_LANG']['composer_client']['install_headline']              = 'Установка пакета';
$GLOBALS['TL_LANG']['composer_client']['install_source']                = 'Исходники';
$GLOBALS['TL_LANG']['composer_client']['installed_headline']            = 'Установленные пакеты';
$GLOBALS['TL_LANG']['composer_client']['installed_in']                  = 'Установлена версия %s';
$GLOBALS['TL_LANG']['composer_client']['mark_and_install']              = 'Установить пакет';
$GLOBALS['TL_LANG']['composer_client']['mark_to_install']               = 'Отметить пакет для установки';
$GLOBALS['TL_LANG']['composer_client']['migrate']                       = 'Миграция';
$GLOBALS['TL_LANG']['composer_client']['migrate_clean']['0']            = 'Удалить пакеты расширений';
$GLOBALS['TL_LANG']['composer_client']['migrate_clean']['1']            = 'Удалить ранее установленные пакеты расширений и начать с чистой установки.';
$GLOBALS['TL_LANG']['composer_client']['migrate_development']['0']      = 'Для разработки';
$GLOBALS['TL_LANG']['composer_client']['migrate_development']['1']      = 'Пакеты будут получены в виде исходников с git, mercurial или svn. Файлы будут установлены как символические ссылки.';
$GLOBALS['TL_LANG']['composer_client']['migrate_do']                    = 'Начать миграцию';
$GLOBALS['TL_LANG']['composer_client']['migrate_faq']                   = '
<h2>Вопросы и ответы</h2>
<ul class="questions">
<li>
	<h3>Необходимо ли использовать этот клиент?</h3>
	Вовсе нет, это необязательно. Но некоторые разработчики распространяют новые возможности или новые расширения только через этот менеджер пакетов.
	Вы можете пропустить некоторые важные обновления, если не будете использовать его.
</li>
<li>
	<h3>Могу ли я установить пакеты, которые доступны в текущем хранилище расширений?</h3>
	Да, вы можете. Все публичные пакеты синхронизированы в новое хранилище расширений (они начинаются с префикса <em>contao-legacy/</em>).<br>
	<em>Обратите внимание, что существующие коммерческие расширения не могут быть установлены с Composer из-за лицензионных ограничений.
	Обратитесь к издателю для включения поддержки Composer.</em>
</li>
<li>
	<h3>Будет ли новое хранилище расширений?</h3>
	Да, новое хранилище расширений существует на <a href="http://legacy-packages-via.contao-community-alliance.org/" target="_blank">legacy-packages-via.contao-community-alliance.org</a>.
	В настоящее время оно представляет собой простой список пакетов установки, но в ближайшее время мы будем улучшать его, под наши потребности.
</li>
<li>
	<h3>Что такое Composer и этот менеджер пакетов Composer?</h3>
	Ответ слишком длинный, чтобы разместить его здесь.  Прочитайте статью о клиенте Composer в <a href="http://de.contaowiki.org/Composer_Client" target="_blank">Contao Wiki</a>.
</li>
<li>
	<h3>Можно ли переключиться обратно на старый менеджер пакетов?</h3>
	Да, можно. Перейдите в окно настроек клиента Composer и выберите "Вернуть старый клиент".
</li>
<li>
	<h3>У меня проблемы с новым клиентом, где я могу получить поддержку?</h3>
	Этот клиент управляется сообществом.
	Вы можете задать свои вопросы на <a href="https://community.contao.org/de/forumdisplay.php?6-Entwickler-Fragen" target="_blank">форумах сообщества</a>,
	в официальном IRC канале <a href="irc://chat.freenode.net/%23contao.composer">#contao.composer</a>
	или <a href="https://github.com/contao-community-alliance/composer/issues" target="_blank">тикет-системе</a>.
</li>
</ul>';
$GLOBALS['TL_LANG']['composer_client']['migrate_intro']                 = '
<p>Уважаемый пользователь, это новый менеджер пакетов Contao, основанный на менеджере зависимостей <a href="http://getcomposer.org/" target="_blank">Composer</a>.</p>
<p>Это фаза публичного бета-тестирования и нам нужна ваша помощь, чтобы проверить работу этого клиента для того, что бы он стал менеджером пакетов Contao по умолчанию.</p>';
$GLOBALS['TL_LANG']['composer_client']['migrate_mode']['0']             = 'Режим миграции';
$GLOBALS['TL_LANG']['composer_client']['migrate_mode']['1']             = 'Мы обнаружили, что здесь установлены некоторые расширения - %d шт. от старого менеджера пакетов. Поэтому, необходимо выбрать, что нужно сделать со старыми пакетами расширений?';
$GLOBALS['TL_LANG']['composer_client']['migrate_none']['0']             = 'Ничего не делать (только для специалистов!)';
$GLOBALS['TL_LANG']['composer_client']['migrate_none']['1']             = 'Ничего не делать, сохранить все там, где оно находится. Это может создать проблемы, выбирайте это только, если вы знаете, что делаете!';
$GLOBALS['TL_LANG']['composer_client']['migrate_preconditions']         = '
<h2>Предварительные условия</h2>
<ul class="preconditions">
<li class="{if smhEnabled==true}fail{else}pass{endif}">Safe Mode Hack {if smhEnabled==true}включен{else}отключен{endif}</li>
<li class="{if allowUrlFopenEnabled==true}pass{else}fail{endif}">Директива allow_url_fopen {if allowUrlFopenEnabled==true}включена{else}отключена{endif}</li>
<li class="{if pharSupportEnabled==true}pass{else}fail{endif}">Поддержка PHAR {if pharSupportEnabled==true}включена{else}отключена{endif}</li>
<li class="{if composerSupported==true}pass{else}fail{endif}">{if composerSupported==true}Вы можете использовать клиент Composer :-){else}Вы не можете использовать клиент Composer :-({endif}</li>
{if commercialPackages!==false}<li class="fail">У вас установлено несколько коммерческих расширений: ##commercialPackages##.<br>Вы можете потерять их в ходе миграции.<br>Пожалуйста, обратитесь к издателю расширения, если он поддерживает Composer, можно продолжить без проблем.</li>{endif}
<li class="{if apcOpcodeCacheEnabled==true}warn{else}pass{endif}">Кэш кода операций APC {if apcOpcodeCacheEnabled==true}включен, это может привести к неожиданным исключениям. Если у вас есть неожиданные ошибки "не удается переопределить класс", попробуйте отключить кэш кода операций APC{elseif apcDisabledByUs==true}временно отключен клиентом Composer{else}отключен{endif}.</li>
</ul>';
$GLOBALS['TL_LANG']['composer_client']['migrate_production']['0']       = 'Для производственного использования';
$GLOBALS['TL_LANG']['composer_client']['migrate_production']['1']       = 'Пакеты будут получены в виде архивов (требуется поддержка zip). Файлы будут установлены в виде копий.';
$GLOBALS['TL_LANG']['composer_client']['migrate_setup']['0']            = 'Настройка конфигурации';
$GLOBALS['TL_LANG']['composer_client']['migrate_setup']['1']            = 'Выберите, для чего будет использована эта установка.';
$GLOBALS['TL_LANG']['composer_client']['migrate_setup_pre']             = '
<h2>Настройка миграции</h2>
<p>Перед началом мы должны задать вам несколько вопросов.</p>';
$GLOBALS['TL_LANG']['composer_client']['migrate_skip']                  = 'Пропустить миграцию (только если вы знаете, что делаете)';
$GLOBALS['TL_LANG']['composer_client']['migrate_skip_confirm']          = 'Пропуск миграции может быть опасным, пропускайте, только если точно знаете, что делаете. Пропустить миграцию прямо сейчас?';
$GLOBALS['TL_LANG']['composer_client']['migrate_upgrade']['0']          = 'Обновление пакетов расширений до совместимости с Composer';
$GLOBALS['TL_LANG']['composer_client']['migrate_upgrade']['1']          = 'Существующие пакеты расширений будут добавлены в менеджер пакетов Composer и переустановлены.<br>';
$GLOBALS['TL_LANG']['composer_client']['no_releasedate']                = '-';
$GLOBALS['TL_LANG']['composer_client']['package_authors']               = 'Разработчик';
$GLOBALS['TL_LANG']['composer_client']['package_dependend_version']     = 'Зависимая версия';
$GLOBALS['TL_LANG']['composer_client']['package_homepage']              = 'Домашняя страница';
$GLOBALS['TL_LANG']['composer_client']['package_installed_version']     = 'Установленная версия';
$GLOBALS['TL_LANG']['composer_client']['package_name']                  = 'Пакет';
$GLOBALS['TL_LANG']['composer_client']['package_requested_version']     = 'Запрошенная версия';
$GLOBALS['TL_LANG']['composer_client']['package_requires']              = 'Зависимости';
$GLOBALS['TL_LANG']['composer_client']['package_suggests']              = 'Предположения';
$GLOBALS['TL_LANG']['composer_client']['package_version']               = 'Версия';
$GLOBALS['TL_LANG']['composer_client']['restartOperation']              = 'Конфигурация Composer была изменена, пожалуйста, повторите последнее действие.';
$GLOBALS['TL_LANG']['composer_client']['save']                          = 'Сохранить';
$GLOBALS['TL_LANG']['composer_client']['search']                        = 'Найти';
$GLOBALS['TL_LANG']['composer_client']['search_headline']               = 'Результаты поиска';
$GLOBALS['TL_LANG']['composer_client']['search_placeholder']            = 'Название пакета или ключевое слово';
$GLOBALS['TL_LANG']['composer_client']['settings_dialog']               = 'Настройки';
$GLOBALS['TL_LANG']['composer_client']['show_dependants']               = 'Показать зависимые пакеты';
$GLOBALS['TL_LANG']['composer_client']['show_dependencies']             = 'Установленные зависимости: %d';
$GLOBALS['TL_LANG']['composer_client']['show_dependency_graph']         = 'Схема зависимостей';
$GLOBALS['TL_LANG']['composer_client']['stability_alpha']               = 'Альфа-версия';
$GLOBALS['TL_LANG']['composer_client']['stability_beta']                = 'Бета-версия';
$GLOBALS['TL_LANG']['composer_client']['stability_dev']                 = 'Релиз разработки';
$GLOBALS['TL_LANG']['composer_client']['stability_rc']                  = 'Релиз-кандидат';
$GLOBALS['TL_LANG']['composer_client']['stability_stable']              = 'Стабильный релиз';
$GLOBALS['TL_LANG']['composer_client']['terminate']                     = 'Завершить';
$GLOBALS['TL_LANG']['composer_client']['undo_migration']                = 'Вернуть старый клиент';
$GLOBALS['TL_LANG']['composer_client']['undo_migration_text']           = '
<p>Уважаемый пользователь, мы сожалеем, что вы решили вернуться к старому клиенту пакетов расширений.</p>
<p>Несколько слов о том, как работает переключатель:</p>
<ul>
<li>Перед переключением, все установленные пакеты будут удалены.</li>
<li>Статус миграции будет сброшен.</li>
<li>Клиент Composer будет отключен.</li>
<li>Клиент хранилища расширений будет включен.</li>
<li>Вам нужно будет переустановить все расширения, которые управляются с помощью клиента хранилища расширений!</li>
</ul>
<br>
';
$GLOBALS['TL_LANG']['composer_client']['update']                        = 'Обновить пакеты';
$GLOBALS['TL_LANG']['composer_client']['update_composer']               = 'Обновить Composer';
$GLOBALS['TL_LANG']['composer_client']['update_database']               = 'Обновить базу данных';
$GLOBALS['TL_LANG']['composer_client']['vcs_requirements']              = '
<ul class="preconditions">
<li class="{if gitAvailable==true}pass{else}fail{endif}">
	git {if gitAvailable==true}доступен{else}отсутствует, большинство пакетов невозможно установить!{endif}
</li>
<li class="{if hgAvailable==true}pass{else}fail{endif}">
	mercurial {if hgAvailable==true}доступен{else}отсутствует, некоторые пакеты невозможно установить!{endif}
</li>
<li class="{if svnAvailable==true}pass{else}fail{endif}">
	svn {if svnAvailable==true}доступен{else}отсутствует, некоторые пакеты невозможно установить!{endif}
</li>
</ul>
';
$GLOBALS['TL_LANG']['composer_client']['widget_minimum_stability']['0'] = 'Минимальная стабильность';
$GLOBALS['TL_LANG']['composer_client']['widget_minimum_stability']['1'] = 'Минимальная стабильность определяет нижний уровень стабильности расширения для установки.';
$GLOBALS['TL_LANG']['composer_client']['widget_prefer_stable']['0']     = 'Предпочитать стабильные';
$GLOBALS['TL_LANG']['composer_client']['widget_prefer_stable']['1']     = 'Если возможно, предпочитать стабильные пакеты, даже если минимальная стабильность ниже, чем стабильный.';
$GLOBALS['TL_LANG']['composer_client']['widget_preferred_install']['0'] = 'Предпочитать установку';
$GLOBALS['TL_LANG']['composer_client']['widget_preferred_install']['1'] = 'Выберите, если предпочитаете пакеты с исходным кодом (требуется git, mercurial или svn), или дистрибутивы архивов (работает каждый раз).';

