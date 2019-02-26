/**
 * Formulaire de demande d'accès.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { Form, FormGroup, Button, Label, Input } from 'reactstrap'

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
			description: '',
		};
	}

	handleAccessChange({ value }) {
		this.setState({ access_id: value });
	}

	handleDescriptionChange({ target: { value } }) {
		this.setState({ description: value });
	}

	handleSubmit(e) {
		const { post } = this.props;
		const { description, access_id } = this.state;
		e.preventDefault();

		post({
			description,
			access_id,
		});
	}

	render() {
		const { access } = this.props;
		const { description } = this.state;

		return (
			<div className="container AccessForm" style={{ overflow: 'visible' }}>
				<h1 className="title">Formulaire de demande d'accès</h1>
				<Form>
					<FormGroup>
						<Label for="access_id">
							Accès demandé
						</Label>
						<Select
							onChange={this.handleAccessChange.bind(this)}
							id="access_id"
							name="access_id"
							placeholder="Type d'accès"
							options={AccessForm.mapSelectionOptions(access)}
							required
						/>
					</FormGroup>

					<FormGroup>
						<Label for="description">
							Description de la demande
						</Label>
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

					<Button type="submit" color="primary" onClick={this.handleSubmit.bind(this)}>
						Réaliser la demande
					</Button>
				</Form>
			</div>
		);
	}
}

export default AccessForm;
