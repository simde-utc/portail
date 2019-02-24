/**
 * Formulaire de demande d'accès.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';

import Select from 'react-select';
import { map } from 'lodash';

class AccessForm extends React.Component {
	static mapSelectionOptions(options) {
		return map(options, ({ id, name }) => ({
			value: id,
			label: name,
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
				<form className="form row" onSubmit={this.handleSubmit.bind(this)}>
					<div className="col-md-6">
						<div className="form-group">
							<label htmlFor="access_id">
								Description de la demande
								<Select
									onChange={this.handleAccessChange.bind(this)}
									id="access_id"
									name="access_id"
									placeholder="Type d'accès"
									options={AccessForm.mapSelectionOptions(access)}
									required
								/>
							</label>
						</div>

						<div className="form-group">
							<label htmlFor="description">
								Description de la demande
								<textarea
									className="form-control"
									id="description"
									name="description"
									rows="3"
									value={description}
									onChange={this.handleDescriptionChange.bind(this)}
									placeholder="Entrez une courte description expliquant la raison de votre demande"
									required
								/>
							</label>
						</div>

						<button type="submit" className="btn btn-primary">
							Réaliser la demande
						</button>
					</div>
				</form>
			</div>
		);
	}
}

export default AccessForm;
