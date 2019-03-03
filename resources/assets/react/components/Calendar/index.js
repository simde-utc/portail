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

	loadEvents(calendar) {
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
				<CalendarCalendar {...this.props} calendars={selectedCalendars} events={events} />
				<span className={`loader large${fetching ? ' active' : ''}`} />
			</div>
		);
	}
}

export default Calendar;
