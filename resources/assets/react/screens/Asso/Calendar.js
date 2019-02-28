/**
 * Affichage les calendriers de l'association.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';

import actions from '../../redux/actions';

import Calendar from '../../components/Calendar';

@connect((store, props) => ({
	config: store.config,
	user: store.getData('user', false),
	calendars: store.getData(['assos', props.asso.id, 'calendars']),
	fetched: store.isFetched(['assos', props.asso.id, 'calendars']),
	fetching: store.isFetching(['assos', props.asso.id, 'calendars']),
}))
class AssoCalendar extends React.Component {
	constructor(props) {
		super(props);

		const { asso } = props;

		if (asso.id) {
			this.loadAssosData(asso.id);
		}
	}

	componentDidUpdate({ asso }) {
		const {
			asso: { id },
		} = this.props;

		if (asso.id !== id) {
			this.loadAssosData(id);
		}
	}

	loadAssosData(id) {
		const { dispatch } = this.props;

		dispatch(actions.definePath(['assos', id, 'calendars']).calendars.all({ owner: `asso,${id}` }));
	}

	render() {
		const { calendars, fetched, asso, config } = this.props;
		config.title = `${asso.shortname} - événements`;

		if (!fetched) {
			return <div />;
		}

		return (
			<div>
				<Calendar calendars={calendars} selectedCalendars={calendars} />
			</div>
		);
	}
}

export default AssoCalendar;
