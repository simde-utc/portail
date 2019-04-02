/**
 * Formulaire de demande d'accès.
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

import Select from 'react-select';
import { map } from 'lodash';

class AccessForm extends React.Component {
	static mapSelectionOptions(options) {
		return map(options, ({ id, name, description }) => ({
			value: id,
			label: `${name} (${description})`,
		}));
	}

	constructor(props) {
		super(props);

		this.state = {
			access_id: null,
			access_name: null,
			description: '',
		};
	}

	componentDidUpdate(lastProps) {
		const { access } = this.props;

		if (lastProps.access.length !== access.length) {
			this.setDefaultAccess(access);
		}
	}

	handleAccessChange({ value, label }) {
		this.setState({ access_id: value, access_name: label });
	}

	handleDescriptionChange({ target: { value } }) {
		this.setState({ description: value });
	}

	handleSubmit(e) {
		const { post } = this.props;
		const { description, access_id } = this.state;
		e.preventDefault();

		if (!description || !access_id) {
			return;
		}

		post({
			description,
			access_id,
		}).then(() => {
			this.cleanInputs();
		});
	}

	cleanInputs() {
		const { access } = this.props;

		this.setState({
			description: '',
		});

		this.setDefaultAccess(access);
	}

	setDefaultAccess(access) {
		const defaultAccess = access.find(element => element.type === 'asso');

		this.setState({
			access_id: defaultAccess.id,
			access_name: defaultAccess.name,
		});
	}

	render() {
		const { access, opened, closeModal } = this.props;
		const { description, access_id, access_name } = this.state;

		return (
			<Modal isOpen={opened}>
				<Form onSubmit={this.handleSubmit.bind(this)}>
					<ModalHeader>Formulaire de demande d'accès</ModalHeader>
					<ModalBody>
						<FormGroup>
							<Label for="access_id">Accès demandé *</Label>
							<Select
								onChange={this.handleAccessChange.bind(this)}
								id="access_id"
								name="access_id"
								placeholder="Type d'accès"
								options={AccessForm.mapSelectionOptions(access)}
								value={{ value: access_id, label: access_name }}
								required
							/>
						</FormGroup>

						<FormGroup>
							<Label for="description">Description de la demande *</Label>
							<Input
								type="textarea"
								id="description"
								name="description"
								rows="3"
								value={description}
								onChange={this.handleDescriptionChange.bind(this)}
								placeholder="Entrez une courte description expliquant la raison de votre demande"
								required
							/>
						</FormGroup>
					</ModalBody>
					<ModalFooter>
						<Button outline className="font-weight-bold" onClick={() => closeModal()}>
							Annuler
						</Button>
						<Button type="submit" className="font-weight-bold" outline color="primary">
							Réaliser la demande
						</Button>
					</ModalFooter>
				</Form>
			</Modal>
		);
	}
}

export default AccessForm;
