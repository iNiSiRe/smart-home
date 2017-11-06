var client = mqtt.connect('ws://' + WS_SERVER);

var room = $('[data-room]:first')[0];
var units = $(room).find('[data-unit]');
var handlers = [];

client.on('connect', function () {

    for (var i = 0; i < units.length; i++) {
        var topic = "units/" + units[i].getAttribute('data-unit-id');

        client.subscribe(topic);

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
                          variables: {
                              enabled: !!(1 == $(this).val())
                          }
                      };
                      client.publish(context.topic, JSON.stringify(message));
                  }
                })(context);

                $(units[i]).find('input[type=checkbox]').on('change', callback);

                handlers[topic] = function (topic, payload) {
                    console.log([topic, payload].join(':'));
                };

            } break;

            case 'TemperatureSensor': {

                handlers[topic] = function (topic, payload) {
                    console.log([topic, payload].join(':'));
                };

            } break;

            case 'Boiler': {

                context = {
                    topic: topic,
                    unit: units[i]
                };

                callback = (function (context) {
                    return function () {
                        var message = {
                            variables: {
                                enabled: !!(1 == $(this).val())
                            }
                        };
                        client.publish(context.topic, JSON.stringify(message));
                    }
                })(context);

                $(units[i]).find('input[type=checkbox]').on('change', callback);

                handlers[topic] = function (topic, payload) {
                    console.log([topic, payload].join(':'));
                };

            } break;
        }
    }

});

client.on('message', function (topic, payload) {

    if (handlers.indexOf(topic) === -1) {
        return true;
    }

    handlers[topic](topic, payload);

});