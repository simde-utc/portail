import React from 'react';
import BigCalendar from 'react-big-calendar';

import { colorFromBackground } from '../../utils';

export default class CalendarCalendar extends React.Component {
	getEvents() {
		const { events, calendars } = this.props;
		const generatedEvents = [];

		Object.keys(events).forEach(calendar_id => {
			const calendar = calendars[calendar_id];

			events[calendar_id].forEach(({ id, name, begin_at, end_at }) => {
				generatedEvents.push({
					id,
					title: name,
					start: new Date(begin_at),
					end: new Date(end_at),
					calendar,
				});
			});
		});
		return generatedEvents;
	}

	static getEventProps(event) {
		return {
			style: {
				backgroundColor: event.calendar.color,
				color: colorFromBackground(event.calendar.color),
			},
		};
	}

	render() {
		return (
			<div style={{ height: '700px' }}>
				<BigCalendar
					defaultView="week"
					events={this.getEvents()}
					eventPropGetter={CalendarCalendar.getEventProps.bind(this)}
				/>
			</div>
		);
	}
}
