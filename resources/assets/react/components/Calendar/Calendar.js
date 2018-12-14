import React, { Fragment } from 'react';
import BigCalendar from 'react-big-calendar';
import moment from 'moment';
import BaseModal from '../Modal';

BigCalendar.setLocalizer(BigCalendar.momentLocalizer(moment));

const INITIAL_STATE = {
    Modal: null,
};

export default class Calendar extends React.Component {
	constructor(props) {
		super(props);
		this.state = INITIAL_STATE;
		this.onSelectedEvent.bind(this);
	}

	getEvents(events) {
		if (events && events.length > 0) {
			return events.map(eventToMap => ({
				id: eventToMap.id,
				title: eventToMap.name,
				start: new Date(eventToMap.begin_at),
				end: new Date(eventToMap.end_at),
				allDay: eventToMap.full_day,
				...eventToMap,
			}));
		}
		return [];
	}

	onSelectedEvent(event) {
		return this.setState({
			Modal: () => (
				<BaseModal
					show
					title={event.title}
          onClose={() => this.setState(INITIAL_STATE)}
				>
					<p>{event.allDay ? 'Toute la journée' : `${moment(event.start).format('HH:mm')} - ${moment(event.end).format('HH:mm')}`}</p>
          <p>{event.location.name} - {event.location.place.name}</p>
				</BaseModal>
			),
		})
	}

	render() {
		const { Modal } = this.state;
		return (
			<Fragment>
        <BigCalendar
            defaultView="week"
            events={ this.getEvents(this.props.events) }
            onSelectEvent={(e) => this.onSelectedEvent(e)}
        />
				{Modal && (<Modal />)}
			</Fragment>
		);
	}
}
