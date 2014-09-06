$(window).addEvent('domready', function () {
    $$('input[type="radio"]').addEvent('change', function () {
        if (this.checked) {
            $$('input[name="' + this.name + '"]').getParent().getParent().removeClass('checked');
            this.getParent().getParent().addClass('checked');
        } else {
            this.getParent().getParent().removeClass('checked');
        }
    });
});
