import React from 'react';
import BigCalendar from 'react-big-calendar';
import moment from 'moment';

BigCalendar.setLocalizer(BigCalendar.momentLocalizer(moment));

export default class Calendar extends React.Component {
	static getEvents(events) {
		if (events && events.length) {
			return events.map(({ id, name, begin_at, end_at }) => ({
				id,
				title: name,
				start: new Date(begin_at),
				end: new Date(end_at),
			}));
		}

		return [];
	}

	render() {
		const { events } = this.props;

		return <BigCalendar defaultView="week" events={Calendar.getEvents(events)} />;
	}
}
