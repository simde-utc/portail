/**
 * Affichage les calendriers de l'association.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { Button } from 'reactstrap';
import { connect } from 'react-redux';
import { NotificationManager } from 'react-notifications';

import actions from '../../redux/actions';

import Calendar from '../../components/Calendar';
import EventForm from '../../components/Calendar/Form';

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

		this.state = {
			openModal: false,
		};

		const { asso, dispatch } = props;

		if (asso.id) {
			this.loadAssosData(asso.id);
		}

		dispatch(actions.config({ title: `${asso.shortname} - événements` }));
	}

	componentDidUpdate({ asso }) {
		const {
			asso: { id, shortname },
			dispatch,
		} = this.props;

		if (asso.id !== id) {
			this.loadAssosData(id);

			dispatch(actions.config({ title: `${shortname} - événements` }));
		}
	}

	loadAssosData(id) {
		const { dispatch } = this.props;

		dispatch(actions.definePath(['assos', id, 'calendars']).calendars.all({ owner: `asso,${id}` }));
	}

	createEvent(data) {
		const { asso, dispatch, calendars } = this.props;

		data.owned_by_type = 'asso';
		data.owned_by_id = asso.id;

		const action = actions.events.create({}, data);

		dispatch(action);

		return action.payload
			.then(() => {
				this.setState({ reloadCalendar: calendars.find(({ id }) => id === data.calendar_id) });
				NotificationManager.success(
					"L'événement a été créé avec succès",
					"Création d'un événément"
				);

				this.setState({ openModal: false });
			})
			.catch(() => {
				NotificationManager.error("L'événément n'a pas pu être créé", "Création d'un événément");

				return Promise.reject();
			});
	}

	render() {
		const { calendars, fetched } = this.props;
		const { openModal, reloadCalendar } = this.state;

		this.state.reloadCalendar = null;

		if (!fetched) {
			return <div />;
		}

		return (
			<div className="container">
				<EventForm
					post={this.createEvent.bind(this)}
					opened={openModal}
					closeModal={() => this.setState({ openModal: false })}
					calendars={calendars}
				/>
				<div className="top-right-button">
					<Button color="primary" outline onClick={() => this.setState({ openModal: true })}>
						Créer un événement
					</Button>
				</div>
				<h1 className="title">Calendriers</h1>
				<Calendar
					calendars={calendars}
					selectedCalendars={calendars}
					reloadCalendar={reloadCalendar}
				/>
			</div>
		);
	}
}

export default AssoCalendar;
