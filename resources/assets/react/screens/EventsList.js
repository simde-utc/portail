import React, { Component } from 'react';
import { connect } from 'react-redux';
import actions from '../redux/actions';
import Calendar from '../components/Calendar';

@connect(store => ({
    events: store.getData('events'),
    fetching: store.isFetching('events'),
    fetched: store.isFetched('events'),
}))
class EventsList extends Component {

    componentWillMount() {
        const { dispatch } = this.props;
        dispatch(actions.events.all());
    }

    render() {
        return (
            <div className="container">
                <h1 className="title">Evenements à venir</h1>
                <div className="content">
                    <span className={"loader large" + (this.props.fetching ? ' active' : '') }></span>
                    {this.props.fetched && (<Calendar events={this.props.events}/>)}
                </div>
            </div>
        );
    }
}

export default EventsList;