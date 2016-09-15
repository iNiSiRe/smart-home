var ws = new WebSocket('ws://' + WS_SERVER);

ws.onopen = function (event) {
    var room = $('[data-room]:first')[0];
    var sensors = $(room).find('[data-sensor]');

    var data = {
        action: 5,
        room: room.getAttribute('data-room-name'),
        sensors: []
    };

    for (var i = 0; i < sensors.length; i++) {
        data.sensors.push(sensors[i].getAttribute('data-sensor-name'));
    }

    ws.send(JSON.stringify(data));

    var controllers = $('[data-controller]');
    for (i = 0; i < controllers.length; i++) {

        $(controllers[i]).find('input[type=range]').on('change', function (controller) {
            return function () {
                var value = $(this).val();
                var data = {
                    "action": 2,
                    "resource": "input",
                    "id": controller.getAttribute('data-module-id'),
                    "name": this.getAttribute('name'),
                    "value": value
                };
                ws.send(JSON.stringify(data));
            }
        }(controllers[i])
        );
    }
};

ws.onmessage = function (event) {
    if (event.data == 'ping') {
        return;
    }

    var data = JSON.parse(event.data);
    var sensor = data['sensor'];
    var room = data['room'];
    var value = data['value'];
    $('[data-sensor-name=' + sensor + '] .panel-body').html(value);

};