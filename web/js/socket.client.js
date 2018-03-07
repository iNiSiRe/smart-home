var client = mqtt.connect('ws://' + WS_SERVER);

var room = $('[data-room]:first')[0];
var units = $(room).find('[data-unit]');
var handlers = [];

handlers['stdout'] = function (topic, message) {

    $("#logs").append("<span>" + message + "</span>");

};

client.subscribe('stdout');

client.on('connect', function () {

    for (var i = 0; i < units.length; i++) {
        var topic = "units/" + units[i].getAttribute('data-unit-id');

        var context, callback;

        switch (units[i].getAttribute('data-unit')) {

            case 'Switch': {

                context = {
                    topic: topic,
                    unit: units[i]
                };

                callback = (function (context) {
                  return function () {

                      var message = {
                          enabled: this.checked
                      };
                      client.publish(context.topic, JSON.stringify(message));

                      return true;
                  }
                })(context);

                $(units[i]).find('input[type=checkbox]').on('change', callback);

                handlers[topic] = ( function ( unit ) {

                    return function (topic, payload) {
                        console.log([topic, payload].join(':'));
                        $(unit).find('input[type=checkbox]').checked = JSON.parse(payload).enabled;
                    }

                })(units[i]);

            } break;

            case 'TemperatureSensor': {

                topic = "units/" + units[i].getAttribute('data-unit-id') + "/indicators";

                handlers[topic] = ( function ( unit ) {

                    return function (topic, payload) {
                        console.log([topic, payload].join(':'));

                        var value = parseFloat(JSON.parse(payload).temperature).toFixed(2);

                        $(unit).find('span#temperature').html(value);
                    }

                })(units[i]);

            } break;

            case 'Boiler': {

                context = {
                    topic: topic,
                    unit: units[i]
                };

                callback = (function (context) {
                    return function () {
                        var message = {
                            enabled: this.checked
                        };

                        console.log(['publish', context.topic, message].join(':'));

                        client.publish(context.topic, JSON.stringify(message));
                    }
                })(context);

                $(units[i]).find('input[type=checkbox]').on('change', callback);

                handlers[topic] = ( function ( unit ) {

                    return function (topic, payload) {
                        console.log([topic, payload].join(':'));
                        $(unit).find('input[type=checkbox]')[0].checked = JSON.parse(payload).enabled;
                    }

                })(units[i]);

            } break;
        }

        client.subscribe(topic);
    }

});

client.on('message', function (topic, payload) {

    if (handlers.hasOwnProperty(topic) === false) {
        return true;
    }

    handlers[topic](topic, payload);

});