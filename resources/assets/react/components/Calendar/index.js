import React from 'react';

import CalendarSelector from './Selector.js';
import CalendarCalendar from './Calendar.js';

export default class Calendar extends React.Component {
	render() {
		return (
			<div className="container Calendar">
				<CalendarSelector />
				<CalendarCalendar events={ this.props.events } />
			</div>
		);
	}
}
