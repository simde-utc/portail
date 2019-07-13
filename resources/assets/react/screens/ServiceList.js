/**
 * Lists services.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';
import { Modal, ModalHeader, ModalBody, ModalFooter, Button } from 'reactstrap';
import { NotificationManager } from 'react-notifications';
import { findIndex } from 'lodash';
import actions from '../redux/actions';

import Service from '../components/Service';

@connect(store => ({
	config: store.config,
	userServices: store.getData('user/services'),
	services: store.getData('services'),
	fetching: store.isFetching('services'),
	fetched: store.isFetched('services'),
}))
class ServiceListScreen extends React.Component {
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
					onClick: () => {},
				},
			},
		};
	}

	componentWillMount() {
		const { dispatch } = this.props;

		dispatch(actions.services.all());
		dispatch(actions.config({ title: 'Listes des services' }));
	}

	getServices(services, userServices) {
		return services.map(service => (
			<Service
				key={service.id}
				service={service}
				isFollowing={findIndex(userServices, ['id', service.id]) > -1}
				follow={() => {
					this.followService(service);
				}}
				unfollow={() => {
					this.unfollowService(service);
				}}
			/>
		));
	}

	followService(service) {
		const { dispatch } = this.props;

		this.setState({
			modal: {
				show: true,
				title: 'Ajouter aux favoris',
				body: (
					<p>
						Souhaitez-vous vraiment ajouter aux favoris le service{' '}
						<span className="font-italic">{service.name}</span> ?
					</p>
				),
				button: {
					type: 'success',
					text: 'Ajouter aux favoris',
					onClick: () => {
						actions.user.services
							.create(
								{},
								{
									service_id: service.id,
								}
							)
							.payload.then(() => {
								dispatch(actions.user.services.all());
								NotificationManager.success(
									`Vous avez ajouté aux favoris le service: ${service.name}`,
									'Suivre un service'
								);
							})
							.catch(() => {
								NotificationManager.error('Une erreur a été rencontrée', 'Suivre un service');
							})
							.finally(() => {
								this.setState(({ modal }) => ({
									modal: { ...modal, show: false },
								}));
							});
					},
				},
			},
		});
	}

	unfollowService(service) {
		const { dispatch } = this.props;

		this.setState({
			modal: {
				show: true,
				title: 'Retirer des favoris',
				body: (
					<p>
						Souhaitez-vous vraiment retirer des favoris le service{' '}
						<span className="font-italic">{service.name}</span> ?
					</p>
				),
				button: {
					type: 'warning',
					text: 'Retirer des favoris',
					onClick: () => {
						actions.user.services
							.delete(service.id)
							.payload.then(() => {
								dispatch(actions.user.services.all());
								NotificationManager.warning(
									`Vous avez retiré des favoris le service: ${service.name}`,
									'Retirer un service'
								);
							})
							.catch(() => {
								NotificationManager.error('Une erreur a été rencontrée', 'Retirer un service');
							})
							.finally(() => {
								this.setState(({ modal }) => ({
									modal: { ...modal, show: false },
								}));
							});
					},
				},
			},
		});
	}

	render() {
		const { fetching, services, userServices } = this.props;
		const {
			modal: { show, title, body, button },
		} = this.state;

		return (
			<div className="container">
				<Modal isOpen={show}>
					<ModalHeader>{title}</ModalHeader>
					<ModalBody>{body}</ModalBody>
					<ModalFooter>
						<Button
							outline
							onClick={() => {
								this.setState(({ modal }) => ({
									modal: { ...modal, show: false },
								}));
							}}
						>
							Annuler
						</Button>
						<Button outline color={button.type} onClick={button.onClick}>
							{button.text}
						</Button>
					</ModalFooter>
				</Modal>

				<h1 className="title">Liste des services</h1>
				<div className="content">
					<span className={`loader large${fetching ? ' active' : ''}`} />
					{this.getServices(services, userServices)}
				</div>
			</div>
		);
	}
}

export default ServiceListScreen;
