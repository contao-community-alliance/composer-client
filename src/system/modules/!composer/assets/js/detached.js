$(window).addEvent('domready', function () {
    var output = $('output');
    var refreshIndicator = $('refresh-indicator');
    var uptime = $('uptime');
    var submit = $('submit');
    var terminate = $('terminate');

    var request = new Request.JSON({
        url: 'contao/main.php?do=composer',
        method: 'get',
        onSuccess: function (responseJSON) {
            output.innerHTML = responseJSON.output;
            uptime.innerHTML = responseJSON.uptime;

            if (responseJSON.isRunning) {
                setTimeout(function () {
                    run();
                }, 10);
            } else {
                submit.setProperty('disabled', false);
                terminate.setProperty('disabled', true);
            }
        }
    });

    var timer = 0;

    function run() {
        timer++;

        refreshIndicator.setStyle('width', timer + '%');

        if (timer >= 100) {
            timer = 0;
            request.send();
        } else {
            setTimeout(function () {
                run();
            }, 10);
        }
    }

    run();
});
