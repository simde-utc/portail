/**
 * List bookings.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { Modal, ModalHeader, ModalBody, ModalFooter, Button, Input } from 'reactstrap';
import DatetimeRangePicker from 'react-datetime-range-picker';
import Select from 'react-select';
import { connect } from 'react-redux';
import { find } from 'lodash';
import { NotificationManager } from 'react-notifications';

import Calendar from '../components/Calendar';

import actions from '../redux/actions';
import { formatDate } from '../utils';

@connect(store => {
	const assos = store.getData(['user', 'assos']);
	const user = store.getData('user', {});
	const permissions = {};
	const fetchedPermissions = [];

	assos.forEach(asso => {
		const path = ['assos', asso.id, 'members', user.id, 'permissions'];

		permissions[asso.id] = store.getData(path);
		fetchedPermissions.push(store.isFetched(path));
	});

	return {
		config: store.config,
		user,
		assos,
		permissions,
		fetchedPermissions,
		types: store.getData(['bookings', 'types']),
		rooms: store.getData('rooms'),
		fetched: store.isFetched('rooms'),
	};
})
class BookingScreen extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			modal: {
				show: false,
				title: '',
				body: '',
				button: {
					type: '',
					text: '',
					disabled: false,
					onClick: () => {},
				},
			},
		};
	}

	componentWillMount() {
		const { user, assos, dispatch } = this.props;

		dispatch(actions.rooms.all());
		dispatch(actions.bookings.types.all());

		assos.forEach(asso => {
			dispatch(
				actions
					.assos(asso.id)
					.members(user.id)
					.permissions.all()
			);
		});

		dispatch(actions.config({ title: 'Planning des réservations' }));
	}

	onSelectingRange(data) {
		this.askBooking(data.start, data.end);
	}

	getAllowedAssos() {
		const { assos, permissions } = this.props;

		return assos.filter(asso => {
			return find(permissions[asso.id], permission => permission.type === 'booking');
		});
	}

	static getAssos(assos) {
		return assos.map(asso => ({
			value: asso.id,
			label: asso.name,
		}));
	}

	static getRooms(rooms) {
		return rooms.map(room => ({
			value: room.id,
			label: room.calendar.name,
		}));
	}

	static getTypes(types) {
		return types.map(type => ({
			value: type.id,
			label: type.name,
		}));
	}

	onClickEvent(event, e){
		console.log("onclick")
		console.log(event)
		console.log(this.getAllowedAssos())
		const isAllowed = this.getAllowedAssos().find((element) => {
			return element.id === event.calendar.owned_by.id
		})

		this.setState(prevState => {
			const { modal } = prevState;
			const spanStyle = {fontWeight: "bold"}
			const divStyle = {margin: "1em 0"}

			modal.show = true;
			modal.title = "Information d'une réservation";
			modal.body = (
				<div>
					<h1>{event.title}</h1>
					<div style={divStyle}>
						<span style={spanStyle}>Salle réservée : </span> {event.calendar.name}
					</div>
					<div style={divStyle}>
						<span style={spanStyle}>Réservation de : </span>{event.calendar.owned_by.name}
					</div>
					<div style={divStyle}>
						<span style={spanStyle}>Contact : </span>{event.calendar.owned_by.login}@assos.utc.fr
					</div>
				</div>
			)
			modal.button.type = "danger"
			modal.button.text = "Supprimer"
			modal.button.disabled = !isAllowed
			
			modal.button.onClick = isAllowed ? () => {
				const action = actions.rooms(event.calendar.id).bookings.remove(event.id);	
				
				action.payload
					.then(({ data }) => {
						NotificationManager.warning('Réservation supprimée avec succès', 'Suppression');

						// On recharge le calendrier
						this.setState({
							reloadCalendar: data.room.calendar,
						});

						this.setState(({ modal }) => ({
							modal: { ...modal, show: false },
						}));
					})
					.catch(({ response: { data: { message } } }) => {
						NotificationManager.error(message, 'Suppression');
					});
			} : null

			return prevState 
		})
	}

	askBooking(begin = new Date(), end = new Date()) {
		this.setState(prevState => {
			const { rooms, types } = this.props;
			const { modal } = prevState;
			const possibleAssos = BookingScreen.getAssos(this.getAllowedAssos());
			const possibleRooms = BookingScreen.getRooms(rooms);
			const possibleTypes = BookingScreen.getTypes(types);

			prevState.begin_at = begin;
			prevState.end_at = end;

			modal.show = true;
			modal.title = "Réservation d'un créneau";
			modal.body = (
				<div>
					Name:
					<Input onChange={e => this.setState({ name: e.target.value })} />
					Créneau:
					<DatetimeRangePicker
						startDate={begin}
						endDate={end}
						onStartDateChange={date => console.log(date) || this.setState({ begin_at: date })}
						onEndDateChange={date => this.setState({ end_at: date })}
						className="d-flex"
						input
						inputProps={{ required: true }}
					/>
					Association:
					<Select
						placeholder=""
						isSearchable
						options={possibleAssos}
						onChange={asso => this.setState({ asso_id: asso.value })}
					/>
					Salle:
					<Select
						placeholder=""
						isSearchable
						options={possibleRooms}
						onChange={room => this.setState({ room_id: room.value })}
					/>
					Type de réservation:
					<Select
						placeholder=""
						isSearchable
						options={possibleTypes}
						onChange={type => this.setState({ type_id: type.value })}
					/>
				</div>
			);
			modal.button.type = 'primary';
			modal.button.text = 'Réserver';
			modal.button.disabled = false

			modal.button.onClick = () => {
				const { room_id, asso_id, type_id, name, begin_at, end_at } = this.state;
				const action = actions.rooms(room_id).bookings.create({
					room_id,
					name,
					type_id,
					begin_at: formatDate(begin_at),
					end_at: formatDate(end_at),
					owned_by_id: asso_id,
					owned_by_type: 'asso',
				});

				action.payload
					.then(({ data }) => {
						NotificationManager.warning('Réservation réalisée avec succès', 'Réservation');

						// On recharge le calendrier
						this.setState({
							reloadCalendar: data.room.calendar,
						});

						this.setState(({ modal }) => ({
							modal: { ...modal, show: false },
						}));
					})
					.catch(({ response: { data: { message } } }) => {
						NotificationManager.error(message, 'Réservation');
					});
			};

			return prevState;
		});
	}

	render() {
		const { user, rooms, fetched, fetchedPermissions } = this.props;
		const { modal, reloadCalendar } = this.state;

		this.state.reloadCalendar = null;

		if (!fetched) {
			return <div />;
		}
		const calendars = rooms.map(room => room.calendar);

		return (
			<div className="container">
				<Modal isOpen={modal.show}>
					<ModalHeader>{modal.title}</ModalHeader>
					<ModalBody>{modal.body}</ModalBody>
					<ModalFooter>
						<Button
							outline
							className="font-weight-bold"
							onClick={() => {
								this.setState(prevState => ({
									...prevState,
									modal: { ...prevState.modal, show: false },
								}));
							}}
						>
							Annuler
						</Button>
						<Button
							className="font-weight-bold"
							outline
							color={modal.button.type}
							onClick={modal.button.onClick}
							disabled={modal.button.disabled}
						>
							{modal.button.text}
						</Button>
					</ModalFooter>
				</Modal>
				<Calendar
					calendars={calendars}
					selectedCalendars={calendars}
					selectable={fetchedPermissions.every(fetched => fetched)}
					onSelectSlot={this.onSelectingRange.bind(this)}
					onSelectEvent={this.onClickEvent.bind(this)}
					reloadCalendar={reloadCalendar}
					scrollToTime={new Date(null, null, null, 8)}
				/>
				{user.types.member && (
					<Button
						className="m-1 btn btn-m font-style-bold col align-self-end mt-3"
						color="primary"
						outline
						disabled={fetchedPermissions.some(fetched => !fetched)}
						onClick={() => this.askBooking()}
					>
						Réserver un créneau
					</Button>
				)}
			</div>
		);
	}
}

export default BookingScreen;
