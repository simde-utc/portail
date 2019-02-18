import React from 'react';

import CalendarSelector from './Selector';
import CalendarCalendar from './Calendar';

const Calendar = ({ events }) => (
	<div className="container Calendar">
		<CalendarSelector />
		<CalendarCalendar events={events} />
	</div>
);

export default Calendar;
