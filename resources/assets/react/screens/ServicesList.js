import React from 'react';
import { connect } from 'react-redux';
import actions from '../redux/actions';
import { Card, CardBody, CardTitle, CardSubtitle, CardFooter } from 'reactstrap';
import { Modal, ModalHeader, ModalBody, ModalFooter, Button } from 'reactstrap';
import { NotificationManager } from 'react-notifications';
import AspectRatio from 'react-aspect-ratio';
import { sortBy, findIndex } from 'lodash';

import Service from '../components/Service';

@connect(store => ({
	userServices: store.getData('user/services'),
	services: store.getData('services'),
	fetching: store.isFetching('services'),
	fetched: store.isFetched('services')
}))
class ServicesListScreen extends React.Component {
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
			}
		};
	}

	componentWillMount() {
		this.props.dispatch(actions.services.all())
	}

	getServices(services, userServices) {
		return services.map(service => (
			<Service key={ service.id } service={ service } isFollowing={ findIndex(userServices, ['id', service.id]) > -1 } follow={() => { this.followService(service) }} unfollow={() => { this.unfollowService(service) }} />
		));
	}

	followService(service) {
		this.setState(prevState => ({
			...prevState,
			modal: {
				show: true,
				title: 'Ajouter aux favoris',
				body:	<p>Souhaitez-vous vraiment ajouter au favoris le service <span className="font-italic">{ service.name }</span> ?</p>,
				button: {
					type: 'success',
					text: 'Ajouter dans les favoris',
					onClick: () => {
						actions.user.services.create({}, {
							service_id: service.id,
						}).payload.then(() => {
							this.props.dispatch(actions.user.services.all())
							NotificationManager.success('Vous avez ajouté aux favoris le service: ' + service.name, 'Suivre un service')
						}).catch(() => {
							NotificationManager.error('Une erreur a été rencontré', 'Suivre un service')
						}).finally(() => {
							this.setState(prevState => ({ ...prevState, modal: { ...prevState.modal, show: false } }));
						});
					}
				}
			}
		}));
	}

	unfollowService(service) {
		this.setState(prevState => ({
			...prevState,
			modal: {
				show: true,
				title: 'Retirer des favoris',
				body:	<p>Souhaitez-vous vraiment retirer des favoris le service <span className="font-italic">{ service.name }</span> ?</p>,
				button: {
					type: 'warning',
					text: 'Retirer des favoris',
					onClick: () => {
						actions.user.services.delete(
							service.id
						).payload.then(() => {
							this.props.dispatch(actions.user.services.all())
							NotificationManager.warning('Vous avez retiré des favoris le service: ' + service.name, 'Retirer un service')
						}).catch(() => {
							NotificationManager.error('Une erreur a été rencontré', 'Retirer un service')
						}).finally(() => {
							this.setState(prevState => ({ ...prevState, modal: { ...prevState.modal, show: false } }));
						});
					}
				}
			}
		}));
	}

	render() {
		return (
			<div className="container">
				<Modal isOpen={ this.state.modal.show }>
					<ModalHeader>{ this.state.modal.title }</ModalHeader>
					<ModalBody>
						{ this.state.modal.body }
					</ModalBody>
					<ModalFooter>
						<Button outline onClick={() => { this.setState(prevState => ({ ...prevState, modal: { ...prevState.modal, show: false }})); } }>Annuler</Button>
						<Button outline color={ this.state.modal.button.type } onClick={ this.state.modal.button.onClick }>{ this.state.modal.button.text }</Button>
					</ModalFooter>
        		</Modal>

				<h1 className="title">Liste des services</h1>
				<div className="content">
					<span className={"loader large" + (this.props.fetching ? ' active' : '') }></span>
					{ this.getServices(this.props.services, this.props.userServices) }
				</div>
			</div>
		);
	}
}

export default ServicesListScreen;
