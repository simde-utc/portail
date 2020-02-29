/**
 * Event creation form.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import {
	Modal,
	ModalBody,
	ModalHeader,
	ModalFooter,
	Form,
	FormGroup,
	Button,
	Label,
	Input,
} from 'reactstrap';
import DatetimeRangePicker from 'react-datetime-range-picker';
import { connect } from 'react-redux';

import Select from 'react-select';
import { map } from 'lodash';

import actions from '../../redux/actions';
import { formatDate } from '../../utils';

@connect(store => ({
	visibilities: store.getData('visibilities'),
}))
class EventForm extends React.Component {
	static mapSelectionOptions(options) {
		return map(options, ({ id, name }) => ({
			value: id,
			label: name,
		}));
	}

	constructor(props) {
		super(props);
		const end_at = new Date();
		end_at.setMinutes(end_at.getMinutes() + 5);

		this.state = {
			name: '',
			begin_at: new Date(),
			end_at,
			calendar_id: null,
			calendar_name: null,
			visibility_id: null,
			visibility_name: null,
		};

		if (props.visibilities.length === 0) {
			props.dispatch(actions.visibilities.get());
		}
	}

	componentDidMount() {
		const { calendars, visibilities } = this.props;

		if (calendars.length !== 0) {
			this.setDefaultCalendar(calendars);
		}

		if (visibilities.length !== 0) {
			this.setDefaultVisibility(visibilities);
		}
	}

	componentDidUpdate(lastProps) {
		const { visibilities, calendars, opened, defaultData } = this.props;

		if (lastProps.visibilities.length !== visibilities.length) {
			this.setDefaultVisibility(visibilities);
		}

		if (lastProps.calendars.length !== calendars.length) {
			this.setDefaultCalendar(calendars);
		}

		// Indicate that we want to open the form.
		if (!lastProps.opened && opened) {
			setTimeout(() => this.setState(defaultData), 10);
		}
	}

	getEvents(events) {
		const { eventFilter } = this.state;

		return EventForm.mapSelectionOptions(
			events.filter(eventToFilter => {
				return eventToFilter.name.indexOf(eventFilter) >= 0;
			})
		);
	}

	setDefaultVisibility(visibilities) {
		const defaultVisibility = visibilities.find(visibility => visibility.type === 'public');

		this.setState({
			visibility_id: defaultVisibility.id,
			visibility_name: defaultVisibility.name,
		});
	}

	setDefaultCalendar(calendars) {
		const defaultCalendar = calendars[0] || {};

		this.setState({
			calendar_id: defaultCalendar.id,
			calendar_name: defaultCalendar.name,
		});
	}

	cleanInputs() {
		const { visibilities, calendars } = this.props;
		const end_at = new Date();
		end_at.setMinutes(end_at.getMinutes() + 5);

		this.setState({
			name: '',
			begin_at: new Date(),
			end_at,
		});

		this.setDefaultVisibility(visibilities);
		this.setDefaultCalendar(calendars);
	}

	handleSubmit(e) {
		const { post } = this.props;
		const { name, begin_at, end_at, calendar_id, visibility_id } = this.state;
		e.preventDefault();

		post({
			name,
			begin_at: formatDate(begin_at),
			end_at: formatDate(end_at),
			calendar_id,
			visibility_id,
		}).then(() => {
			this.cleanInputs();
		});
	}

	handleSearchEvent(value) {
		this.setState({ eventFilter: value });
	}

	handleChange(e) {
		e.persist();

		this.setState({ [e.target.name]: e.target.value });
	}

	handleVisibilityChange({ value, label }) {
		this.setState({ visibility_id: value, visibility_name: label });
	}

	handleCalendarChange({ value, label }) {
		this.setState({ calendar_id: value, calendar_name: label });
	}

	render() {
		const { opened, visibilities, calendars, closeModal } = this.props;
		const {
			name,
			begin_at,
			end_at,
			calendar_id,
			calendar_name,
			visibility_id,
			visibility_name,
		} = this.state;

		return (
			<Modal className="modal-dialog-extended" isOpen={opened}>
				<Form onSubmit={this.handleSubmit.bind(this)}>
					<ModalHeader toggle={closeModal.bind(this)}>Créer un événement</ModalHeader>
					<ModalBody>
						<FormGroup>
							<Label for="access_id">Titre *</Label>
							<Input
								type="text"
								className="form-control"
								id="name"
								name="name"
								value={name}
								onChange={e => this.handleChange(e)}
								placeholder="Titre de l'événément"
								required
							/>
						</FormGroup>

						<FormGroup>
							<Label for="access_id">Créneau *</Label>
							<DatetimeRangePicker
								startDate={begin_at}
								endDate={end_at}
								onStartDateChange={date => this.setState({ begin_at: date })}
								onEndDateChange={date => this.setState({ end_at: date })}
								input
								className="d-flex"
								required
							/>
						</FormGroup>

						<FormGroup>
							<Label for="calendar_id">Calendrier *</Label>
							<Select
								onChange={this.handleCalendarChange.bind(this)}
								name="calendar_id"
								placeholder="Calendrier associé"
								options={EventForm.mapSelectionOptions(calendars)}
								value={{ value: calendar_id, label: calendar_name }}
							/>
						</FormGroup>

						<FormGroup>
							<Label for="visibility_id">Visibilité *</Label>
							<Select
								onChange={this.handleVisibilityChange.bind(this)}
								name="visibility_id"
								placeholder="Visibilité de l'événement"
								options={EventForm.mapSelectionOptions(visibilities)}
								value={{ value: visibility_id, label: visibility_name }}
							/>
						</FormGroup>
					</ModalBody>
					<ModalFooter>
						<Button
							className="btn-reinit"
							outline
							color="danger"
							onClick={() => this.cleanInputs()}
						>
							Réinitialiser
						</Button>
						<Button outline onClick={() => closeModal()}>
							Annuler
						</Button>
						<Button type="submit" outline color="primary">
							Créer l'événement
						</Button>
					</ModalFooter>
				</Form>
			</Modal>
		);
	}
}

export default EventForm;
