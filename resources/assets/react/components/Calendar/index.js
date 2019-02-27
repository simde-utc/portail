import React from 'react';
import { connect } from 'react-redux';

import CalendarSelector from './Selector';
import CalendarCalendar from './Calendar';

import actions from '../../redux/actions';

@connect()
class Calendar extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			selectedCalendars: {},
			events: {},
		};

		if (props.selectedCalendars) {
			props.selectedCalendars.forEach(calendar => {
				const { selectedCalendars } = this.state;
				selectedCalendars[calendar.id] = calendar;

				this.loadEvents(calendar);
			});
		}
	}

	loadEvents(calendar) {
		const { dispatch } = this.props;
		const action = actions.calendars(calendar.id).events.all();

		dispatch(action);
		action.payload.then(({ data }) => {
			this.setState(prevState => {
				if (prevState.selectedCalendars[calendar.id]) {
					prevState.events[calendar.id] = data;
				}

				return prevState;
			});
		});
	}

	addCalendar(calendar) {
		this.setState(
			prevState => {
				prevState.selectedCalendars[calendar.id] = calendar;

				return prevState;
			},
			() => this.loadEvents(calendar)
		);
	}

	removeCalendar(calendar) {
		this.setState(prevState => {
			delete prevState.selectedCalendars[calendar.id];
			delete prevState.events[calendar.id];

			return prevState;
		});
	}

	render() {
		const { calendars } = this.props;
		const { selectedCalendars, events } = this.state;

		return (
			<div className="container Calendar">
				<CalendarSelector
					calendars={calendars}
					selectedCalendars={selectedCalendars}
					onAddCalendar={this.addCalendar.bind(this)}
					onRemoveCalendar={this.removeCalendar.bind(this)}
				/>
				<CalendarCalendar {...this.props} calendars={selectedCalendars} events={events} />
			</div>
		);
	}
}

export default Calendar;
