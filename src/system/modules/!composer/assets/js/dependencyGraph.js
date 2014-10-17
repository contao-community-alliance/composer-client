function toggleGroup(group) {
    var toggler = $$('a[data-group="' + group + '"]')[0];
    if (toggler.get('data-expanded') == 'true') {
        $$('tr.' + group + '_item').setStyle('display', 'none');
        toggler.set('data-expanded', false);
        toggler.getElement('img').set('src', 'system/modules/!composer/assets/images/expand.png');
    } else {
        $$('tr.' + group + '_item').setStyle('display', '');
        toggler.set('data-expanded', true);
        toggler.getElement('img').set('src', 'system/modules/!composer/assets/images/collapse.png');
    }
    var index = 1;
    $$('.tl_listing tr').each(function (tr) {
        if (tr.getStyle('display') != 'none') {
            tr.removeClass('odd');
            tr.removeClass('even');
            tr.addClass(index++ % 2 == 0 ? 'even' : 'odd');
        }
    });
}