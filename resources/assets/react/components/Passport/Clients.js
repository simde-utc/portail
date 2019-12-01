import React, { Component } from 'react';

import Modal from '../Modal';
import Client from './Client';

class Clients extends Component {
	constructor(props) {
		super(props);

		this.state = {
			clients: [],
			client: [],
			scopes: [],
			form: {
				errors: [],
				name: '',
				asso_id: 1,
				redirect: '',
				scopes: [],
			},
			togglers: {
				creationModal: false,
				detailModal: false,
				editionModal: false,
			},
		};
		this.viewClient.bind(this);
		this.editClient.bind(this);
	}

	componentDidMount() {
		this.getClients();
		this.getTokens();
	}

	getClients() {
		axios.get('/oauth/clients').then(response => {
			const clients = response.data;

			clients.map(client => {
				try {
					client.scopes = JSON.parse(client.scopes);

					if (client.scopes === null) {
						client.scopes = [];
					}
				} catch (error) {
					console.error(error);
					client.scopes = [];
				}

				return client;
			});

			this.setState({ clients });
		});
	}

	getTokens() {
		axios.get('/oauth/scopes').then(response => {
			const scopes = [];

			Object.keys(response.data).forEach(key => {
				if (key.startsWith('client')) {
					scopes.push({
						name: key,
						description: response.data[key],
					});
				}
			});

			this.setState({ scopes });
		});
	}

	handleSubmit(method, url, e) {
		e.preventDefault();

		const { form } = this.state;
		form.errors = [];

		axios({ method, url, data: form })
			.then(() => {
				this.getClients();

				const form = {
					errors: [],
					name: '',
					asso_id: '',
					redirect: '',
					scopes: [],
				};

				this.setState({ form });

				//	$('#creationModal').modal('toggle');
			})
			.catch(() => {
				form.errors = ['Une erreur est survenue. Veuillez réessayer'];
				this.setState({ form });
			});
	}

	handleModalClose(key) {
		this.toggle(key, false);
	}

	toggle(key, force = null) {
		this.setState(prevState => ({
			togglers: {
				[key]: force == null ? !prevState.togglers[key] : force,
			},
		}));
	}

	/*
	|--------------------------------------------------------------------------
	|	Client Controllers
	|--------------------------------------------------------------------------
	| View, edit and delete
	*/

	viewClient(client) {
		this.setState({ client });
		this.toggle('detailModal', true);
	}

	editClient(client) {
		this.setState({ client });
		this.toggle('editionModal', true);
	}

	deleteClient(client) {
		this.handleModalClose('editionModal');
		axios.delete(`/oauth/clients/${client.id}`).then(() => {
			this.getClients();
		});
	}

	handleInputChange(e) {
		const { name, value } = e.target;
		const { form: oldForm } = this.state;

		const form = {
			errors: oldForm.errors,
			name: oldForm.name,
			asso_id: oldForm.asso_id,
			redirect: oldForm.redirect,
			scopes: oldForm.scopes,
		};

		if (name === 'scope') {
			const i = form.scopes.indexOf(value);
			if (i === -1) {
				form.scopes.push(value);
			} else {
				form.scopes.splice(i, 1);
			}
		} else {
			form[name] = e.target.value;
		}

		this.setState({ form });
	}

	render() {
		const { togglers, form, client, clients, scopes } = this.state;

		// Modals
		const clientCreationModal = (
			<Modal
				show={togglers.creationModal}
				title={<h4>Créer un client</h4>}
				onClose={this.handleModalClose.bind(this, 'creationModal')}
			>
				{form.errors.length > 0 ? (
					<div className="alert alert-danger">
						<p className="mb-0">
							<strong>Erreur</strong>
						</p>
						<br />
						Une erreur est survenue. Veuillez réessayer.
					</div>
				) : (
					<span />
				)}

				<form onSubmit={e => this.handleSubmit('post', 'oauth/clients', e)}>
					<div className="form-group row">
						<label className="col-md-3 col-form-label">Nom :</label>

						<div className="col-md-9">
							<input
								id="create-client-name"
								type="text"
								className="form-control"
								name="name"
								onChange={e => this.handleInputChange(e)}
							/>
							<span className="form-text text-muted">
								Le nom qui s'affichera pour vos utilisateurs.
							</span>
						</div>
					</div>

					<div className="form-group row">
						<label className="col-md-3 col-form-label">ID Asso :</label>
						<div className="col-md-9">
							<input
								name="asso_id"
								type="number"
								min="0"
								className="form-control"
								onChange={e => this.handleInputChange(e)}
							/>
							<span className="form-text text-muted">
								L'ID de l'asso pour qui la clé est créee.
							</span>
						</div>
					</div>

					<div className="form-group row">
						<label className="col-md-3 col-form-label">Redirection :</label>
						<div className="col-md-9">
							<input
								type="text"
								className="form-control"
								name="redirect"
								onChange={e => this.handleInputChange(e)}
							/>
							<span className="form-text text-muted">
								Adresse de redirection après authentification.
							</span>
						</div>
					</div>

					<div className="form-group row">
						<label className="col-md-3 col-form-label">Scopes :</label>
						<div className="col-md-9" style={{ overflowY: 'auto', maxHeight: '300px' }}>
							{scopes.length > 0
								? scopes.map(scope => (
										<div key={scope.id} className="checkbox">
											<label>
												<input
													type="checkbox"
													name="scope"
													value={scope.name}
													onChange={e => this.handleInputChange(e)}
												/>
												&nbsp;
												<span
													data-toggle="tooltip"
													data-placement="right"
													title={scope.description}
												>
													{scope.name}
												</span>
											</label>
										</div>
								  ))
								: null}
						</div>
					</div>

					<div className="d-flex justify-content-between">
						<button
							type="button"
							className="btn btn-light"
							onClick={this.handleModalClose.bind(this, 'creationModal')}
						>
							Annuler
						</button>
						<button type="submit" className="btn btn-light">
							Créer le client
						</button>
					</div>
				</form>
			</Modal>
		);
		const clientDetailModal = (
			<Modal
				show={togglers.detailModal}
				title={<h4>Client {client.name}</h4>}
				onClose={this.handleModalClose.bind(this, 'detailModal')}
			>
				<form>
					<div className="form-group row">
						<label className="col-md-3 col-form-label">Nom :</label>
						<div className="col-md-9">
							<input type="text" disabled className="form-control" value={client.name} />
							<span className="form-text text-muted">
								Le nom qui s'affichera pour vos utilisateurs.
							</span>
						</div>
					</div>

					<div className="form-group row">
						<label className="col-md-3 col-form-label">ID Asso :</label>
						<div className="col-md-9">
							<input
								type="number"
								min="0"
								disabled
								className="form-control"
								value={client.asso_id}
							/>
							<span className="form-text text-muted">
								L'ID de l'asso pour qui la clé est créee.
							</span>
						</div>
					</div>

					<div className="form-group row">
						<label className="col-md-3 col-form-label">Redirection :</label>
						<div className="col-md-9">
							<input type="text" className="form-control" disabled value={client.redirect} />
							<span className="form-text text-muted">
								Adresse de redirection après authentification.
							</span>
						</div>
					</div>

					<div className="form-group row" v-if="form.scopes.length > 0">
						<label className="col-md-3 col-form-label">Scopes :</label>
						<div className="col-md-9" style={{ overflowY: 'auto', maxHeight: '300px' }}>
							{client.scopes ? (
								client.scopes.map(scope => (
									<span key={scope.id} className="d-block mb-1">
										<code>{scope}</code> : scopes[scope]
									</span>
								))
							) : (
								<span>Pas de scopes client.</span>
							)}
						</div>
					</div>
				</form>

				<div className="d-flex justify-content-end">
					<button
						type="button"
						className="btn btn-light"
						onClick={this.handleModalClose.bind(this, 'detailModal')}
					>
						Fermer
					</button>
				</div>
			</Modal>
		);
		const clientEditionModal = (
			<Modal
				show={togglers.editionModal}
				title={<h4>Modifier un client</h4>}
				onClose={this.handleModalClose.bind(this, 'editionModal')}
			>
				{form.errors.length > 0 ? (
					<div className="alert alert-danger" v-if="form.errors.length > 0">
						<p className="mb-0">
							<strong>Erreur</strong>
						</p>
						<br />
						Une erreur est survenue. Veuillez réessayer.
					</div>
				) : (
					<span />
				)}

				<form onSubmit={e => this.handleSubmit('post', 'oauth/clients', e)}>
					<div className="form-group row">
						<label className="col-md-3 col-form-label">Nom :</label>

						<div className="col-md-9">
							<input
								id="create-client-name"
								type="text"
								className="form-control"
								name="name"
								value={client.name}
								onChange={e => this.handleInputChange(e)}
							/>

							<span className="form-text text-muted">
								Le nom qui s'affichera pour vos utilisateurs.
							</span>
						</div>
					</div>

					<div className="form-group row">
						<label className="col-md-3 col-form-label">ID Asso :</label>

						<div className="col-md-9">
							<input
								name="asso_id"
								type="number"
								min="0"
								className="form-control"
								value={client.asso_id}
								onChange={e => this.handleInputChange(e)}
							/>

							<span className="form-text text-muted">
								L'ID de l'asso pour qui la clé est créee.
							</span>
						</div>
					</div>

					<div className="form-group row">
						<label className="col-md-3 col-form-label">Redirection :</label>

						<div className="col-md-9">
							<input
								type="text"
								className="form-control"
								name="redirect"
								value={client.redirect}
								onChange={e => this.handleInputChange(e)}
							/>

							<span className="form-text text-muted">
								Adresse de redirection après authentification.
							</span>
						</div>
					</div>

					<div className="form-group row">
						<label className="col-md-3 col-form-label">Scopes :</label>

						<div className="col-md-9">
							{scopes.length > 0
								? scopes.map(scope => {
										let isChecked = false;
										if (client.scopes && client.scopes.indexOf(scope.name) !== -1) isChecked = true;

										return (
											<div key={scope.id} className="checkbox">
												<label>
													<input
														type="checkbox"
														name="scope"
														value={scope.name}
														onChange={e => this.handleInputChange(e)}
														checked={isChecked ? 'checked' : false}
													/>
													&nbsp;
													<span
														data-toggle="tooltip"
														data-placement="right"
														title={scope.description}
													>
														{scope.name}
													</span>
												</label>
											</div>
										);
								  })
								: null}
						</div>
					</div>

					<div className="text-right">
						<button
							type="button"
							className="btn btn-light"
							onClick={this.handleModalClose.bind(this, 'editionModal')}
						>
							Annuler
						</button>
						<button
							type="button"
							className="btn btn-danger mr-2"
							onClick={this.deleteClient.bind(this, client)}
						>
							Supprimer
						</button>
						<button type="submit" className="btn btn-light">
							Modifier le client
						</button>
					</div>
				</form>
			</Modal>
		);

		return (
			<div>
				<div className="card drop-shadow mb-4">
					<div className="card-body">
						<div className="row">
							<div className="col-6">
								<h5>Clients OAuth</h5>
							</div>

							<div className="col-6 text-right">
								<button
									type="button"
									className="btn btn-light"
									onClick={this.toggle.bind(this, 'creationModal', true)}
								>
									Créer un client
								</button>
							</div>
						</div>

						{clients.length > 0 ? (
							clients.map(client => (
								<Client
									key={client.id}
									client={client}
									editClient={this.editClient.bind(this)}
									viewClient={this.viewClient.bind(this)}
								/>
							))
						) : (
							<p className="mt-3 mb-0">Vous n'avez pas encore créé de client OAuth.</p>
						)}
					</div>
				</div>

				{clientCreationModal}
				{clientDetailModal}
				{clientEditionModal}
			</div>
		);
	}
}

export default Clients;
