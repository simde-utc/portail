import React from 'react';
import BigCalendar from 'react-big-calendar';
import moment from 'moment';

BigCalendar.setLocalizer(BigCalendar.momentLocalizer(moment));

export default class Calendar extends React.Component {
	getEvents(events) {
		if (events && events.length > 0) {
			return events.map(eventToMap => ({
				id: eventToMap.id,
				title: eventToMap.name,
				start: new Date(eventToMap.begin_at),
				end: new Date(eventToMap.end_at),
			}));
		}

		return [];
	}

	render() {
		return (
			<BigCalendar
				defaultView="week"
				events={ this.getEvents(this.props.events) }
			/>
		);
	}
}
