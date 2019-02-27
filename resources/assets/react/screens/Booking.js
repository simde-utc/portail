/**
 * Liste les réservations
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';
import { NavLink, Route, Switch } from 'react-router-dom';
import Calendar from '../components/Calendar';

import actions from '../redux/actions';

@connect(store => {
	const assos = store.getData(['user', 'assos']);
	const user = store.getData('user', {});
	const permissions = {};

	assos.forEach(asso => {
		permissions[asso.id] = store.getData(['assos', asso.id, 'members', user.id, 'permissions']);
	});

	return {
		config: store.config,
		user,
		assos,
		permissions,
		rooms: store.getData('rooms'),
		assos: store.getData(['user', 'assos']),
		fetched: store.isFetched('rooms'),
	};
})
class BookingScreen extends React.Component {
	componentWillMount() {
		const { dispatch } = this.props;

		dispatch(actions.rooms.all());
	}

	componentDidUpdate({ assos: prevAssos }) {
		const { user, assos, dispatch } = this.props;

		if (prevAssos !== assos) {
			assos.forEach(asso => {
				dispatch(
					actions
						.assos(asso.id)
						.members(user.id)
						.permissions.all()
				);
			});
		}
	}

	onSelectingRange(data) {
		console.log(data);
	}

	render() {
		const { rooms, fetched, config } = this.props;
		config.title = 'Planning des réservations';

		if (!fetched) {
			return <div />;
		}

		const calendars = rooms.map(room => room.calendar);

		return (
			<Calendar
				calendars={calendars}
				selectedCalendars={calendars}
				selectable
				onSelectSlot={this.onSelectingRange.bind(this)}
			/>
		);
	}
}

export default BookingScreen;
