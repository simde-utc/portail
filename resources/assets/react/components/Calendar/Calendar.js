/**
 * Calendar component.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import BigCalendar from 'react-big-calendar';
import moment from 'moment';

import { colorFromBackground } from '../../utils';

const localizer = BigCalendar.momentLocalizer(moment);

const messages = {
	allDay: 'Journée',
	previous: 'Précédent',
	next: 'Suivant',
	today: "Aujourd'hui",
	month: 'Mois',
	week: 'Semaine',
	day: 'Jour',
	agenda: 'Agenda',
	date: 'Date',
	time: 'Heure',
	event: 'Événement', // Or anything you want
	showMore: total => `+ ${total} événement(s) supplémentaire(s)`,
};

export default class CalendarCalendar extends React.Component {
	getEvents() {
		const { events, calendars } = this.props;
		const generatedEvents = [];

		Object.keys(events).forEach(calendar_id => {
			const calendar = calendars[calendar_id];

			events[calendar_id].forEach(({ id, name, begin_at, end_at, owned_by }) => {
				generatedEvents.push({
					id,
					title: `${owned_by.shortname} - ${name}`,
					start: new Date(begin_at),
					end: new Date(end_at),
					calendar,
				});
			});
		});
		return generatedEvents;
	}

	static getDefaultView() {
		return window.innerWidth > 500 ? 'week' : 'day';
	}

	static getEventProps(event) {
		return {
			style: {
				backgroundColor: event.calendar.color,
				color: colorFromBackground(event.calendar.color),
				border: 'none',
				fontSize: '12px',
			},
		};
	}

	render() {
		return (
			<div style={{ height: '700px' }}>
				<BigCalendar
					localizer={localizer}
					defaultView={CalendarCalendar.getDefaultView()}
					eventPropGetter={CalendarCalendar.getEventProps}
					{...this.props}
					events={this.getEvents()}
					messages={messages}
				/>
			</div>
		);
	}
}
