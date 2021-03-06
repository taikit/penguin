var $ = require("jquery");
var Constants = require('../constants/Constants');
var Dispatcher = require('../dispatcher/Dispatcher');
var ActionTypes = Constants.ActionTypes;

var API = function (model, action, data) {
    return $.ajax({
        url: Constants.APIEndpoint + '?model=' + model + '&action=' + action,
        dataType: 'json',
        type: 'POST',
        data: {data: JSON.stringify(data)},
        success: function (event) {
            console.log('API:' + model + '/' + action);
            console.log(data);
            console.log(event);
            if (event.data) {
                Dispatcher.dispatch({
                    type: ActionTypes.AUTO_STATUS,
                    current_user_id: event.session.user_id
                });
            } else {
                APIError(event);
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log(xhr);
            console.log(textStatus);
            console.log(errorThrown);
        }
    });
};

function APIError(event) {
    console.error("APIError");
    console.error(event);
}

module.exports = {

    login: function (email, password) {
        var data = {
            email: email,
            password: password
        };
        return API('user', 'login', data);
    },

    logout: function () {
        var data = {};
        return API('user', 'logout', data);
    },

    status: function () {
        var data = {};
        return API('user', 'status', data);
    },

    signup: function (email, password, name) {
        var data = {
            email: email,
            password: password,
            name: name
        };
        return API('user', 'create', data);
    },

    get_rooms: function () {
        var data = {};
        return API('room', 'index', data);
    },

    get_messages: function (room_id, last_message_id) {
        var data = {
            room_id: room_id,
            last_message_id: last_message_id
        };
        return API('message', 'index', data);
    },
    create_message: function (message, room_id) {
        var data = {
            content: message,
            room_id: room_id
        };
        return API('message', 'create', data);
    }
};