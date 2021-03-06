var React = require('react');
var MessageStore = require('../stores/MessageStore');
var MessageActionCreators = require('../actions/MessageActionCreators');
var RoomStore = require('../stores/RoomStore');

var MessageOutbox = React.createClass({
    getInitialState: function () {
        return {
            message: ''
        };
    },
    render: function () {
        return (
            <form className="outbox" onSubmit={this._onSubmitMessage}>
                <input type="text"
                       placeholder="Message..."
                       value={this.state.message}
                       onChange={this._onChangeMessage}
                />
                <input type="submit" value="submit"/>
            </form >
        );
    },
    _onChangeMessage: function (event, value) {
        this.setState({message: event.target.value});
    },
    _onSubmitMessage: function (event) {
        event.preventDefault();
        this.setState({message: ''});
        if (this.state.message != '') {
            MessageActionCreators.submit(this.state.message, RoomStore.current_id());
        }
    }
});

module.exports = MessageOutbox;
