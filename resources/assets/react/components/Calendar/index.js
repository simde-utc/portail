/**
 * Calendar component.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Corentin Mercier <corentin@cmercier.fr>
 * @author Paco Pompeani <paco.pompeani@etu.utc.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';
import BigCalendar from 'react-big-calendar';
import moment from 'moment';

import CalendarSelector from './Selector';
import { colorFromBackground } from '../../utils';

import actions from '../../redux/actions';

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

@connect()
class Calendar extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			selectedCalendars: {},
			loadingCalendars: {},
			events: {},
		};

		if (props.selectedCalendars) {
			props.selectedCalendars.forEach(calendar => {
				const { selectedCalendars, loadingCalendars } = this.state;
				selectedCalendars[calendar.id] = calendar;
				loadingCalendars[calendar.id] = true;

				this.loadEvents(calendar);
			});
		}
	}

	componentDidUpdate({ reloadCalendar }) {
		if (reloadCalendar) {
			this.addCalendar(reloadCalendar);
		}
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

	getEvents() {
		const { events, selectedCalendars } = this.state;
		const generatedEvents = [];

		Object.keys(events).forEach(calendar_id => {
			const calendar = selectedCalendars[calendar_id];

			events[calendar_id].forEach(({ id, name, begin_at, end_at, owned_by }) => {
				generatedEvents.push({
					id,
					title: owned_by ? `${owned_by.shortname} - ${name}` : name,
					start: new Date(begin_at),
					end: new Date(end_at),
					calendar,
				});
			});
		});
		return generatedEvents;
	}

	loadEvents(calendar) {
		// const action = actions.calendars(calendar.id).events.all({ 'month|day|week': new Date().'YYYY-MM-DD' });
		const { dispatch } = this.props;
		const action = actions.calendars(calendar.id).events.all();

		dispatch(action);
		action.payload
			.then(({ data }) => {
				this.setState(prevState => {
					prevState.loadingCalendars[calendar.id] = false;

					if (prevState.selectedCalendars[calendar.id]) {
						prevState.events[calendar.id] = data;
					}

					return prevState;
				});
			})
			.catch(() => {
				this.setState(prevState => {
					prevState.loadingCalendars[calendar.id] = false;

					if (prevState.selectedCalendars[calendar.id]) {
						prevState.events[calendar.id] = [];
					}

					return prevState;
				});
			});
	}

	addCalendar(calendar) {
		this.setState(
			prevState => {
				prevState.selectedCalendars[calendar.id] = calendar;
				prevState.loadingCalendars[calendar.id] = true;

				return prevState;
			},
			() => this.loadEvents(calendar)
		);
	}

	removeCalendar(calendar) {
		this.setState(prevState => {
			delete prevState.selectedCalendars[calendar.id];
			delete prevState.loadingCalendars[calendar.id];
			delete prevState.events[calendar.id];

			return prevState;
		});
	}

	render() {
		const { calendars } = this.props;
		const { selectedCalendars, loadingCalendars, events } = this.state;
		const fetching = Object.keys(selectedCalendars).length !== Object.keys(events).length;

		return (
			<div className="container Calendar">
				<CalendarSelector
					calendars={calendars}
					selectedCalendars={selectedCalendars}
					loadingCalendars={loadingCalendars}
					onAddCalendar={this.addCalendar.bind(this)}
					onRemoveCalendar={this.removeCalendar.bind(this)}
				/>
				<div style={{ height: '700px' }}>
					<BigCalendar
						localizer={localizer}
						defaultView={Calendar.getDefaultView()}
						eventPropGetter={Calendar.getEventProps}
						{...this.props}
						events={this.getEvents()}
						messages={messages}
					/>
				</div>
				<span className={`loader large${fetching ? ' active' : ''}`} />
			</div>
		);
	}
}

export default Calendar;
