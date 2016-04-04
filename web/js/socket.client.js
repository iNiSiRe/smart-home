var ws = new WebSocket('ws://192.168.1.109:8000')
ws.onopen = function (event) {
    var room = $('[data-room]:first')[0];
    var sensors = $(room).find('[data-sensor]');

    var data = {
        action: "listen",
        room: room.getAttribute('data-room-name'),
        sensors: []
    };

    for (var i = 0; i < sensors.length; i++) {
        data.sensors.push(sensors[i].getAttribute('data-sensor-name'));
    }

    ws.send(JSON.stringify(data));

    var controllers = $('[data-controller]');
    for (i = 0; i < controllers.length; i++) {
        $(controllers[i]).find('input[type=range]').on('change', function () {
            var value = $(this).val();
            var data = {
                "action": "emit",
                "resource": "input",
                "room": room.getAttribute('data-room-name'),
                "name": this.getAttribute('name'),
                "value": value
            };
            ws.send(JSON.stringify(data));
        });
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
}