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
				scopes: []
			},
			togglers: {
				creationModal: false,
				detailModal: false,
				editionModal: false
			}
		}
		this.viewClient.bind(this);
		this.editClient.bind(this);
	}

	componentDidMount() {
		this.getClients();
		this.getTokens();
	}

	toggle(key, force = null) {
		this.setState(prevState => ({
			togglers: {
				[key]: (force == null) ? !prevState.togglers[key] : force
			}
		}));
	}

	handleModalClose(key) {
		this.toggle(key, false);
	}

	render() {
		// Modals
		const clientCreationModal = (
			<Modal show={ this.state.togglers.creationModal } 
				title={<h4>Créer un client</h4>}
				onClose={ this.handleModalClose.bind(this, 'creationModal')}
			>				
				{ this.state.form.errors.length > 0 ? (
					<div className="alert alert-danger">
						<p className="mb-0"><strong>Erreur</strong></p>
						<br />
						Une erreur est survenue. Veuillez réessayer.
					</div>
				) : (<span></span>)}

				<form onSubmit={ (e) => this.handleSubmit('post', 'oauth/clients', e) }>
					<div className="form-group row">
						<label className="col-md-3 col-form-label">Nom :</label>

						<div className="col-md-9">
							<input id="create-client-name" type="text" className="form-control" name="name" 
								onChange={ (e) => this.handleInputChange(e) } />
							<span className="form-text text-muted">Le nom qui s'affichera pour vos utilisateurs.</span>
						</div>
					</div>

					<div className="form-group row">
						<label className="col-md-3 col-form-label">ID Asso :</label>
						<div className="col-md-9">
							<input name="asso_id" type="number" min="0" className="form-control" onChange={ (e) => this.handleInputChange(e) } />
							<span className="form-text text-muted">
								L'ID de l'asso pour qui la clé est créee.
							</span>
						</div>
					</div>

					<div className="form-group row">
						<label className="col-md-3 col-form-label">Redirection :</label>
						<div className="col-md-9">
							<input type="text" className="form-control" name="redirect" onChange={ (e) => this.handleInputChange(e) } />
							<span className="form-text text-muted">
								Adresse de redirection après authentification.
							</span>
						</div>
					</div>

					<div className="form-group row">
						<label className="col-md-3 col-form-label">Scopes :</label>
						<div className="col-md-9" style={{ overflowY: 'auto', maxHeight: '300px' }}>
							{ this.state.scopes.length > 0 ? (
								this.state.scopes.map((scope, i) =>
									<div key={i} className="checkbox">
										<label>
											<input type="checkbox" name="scope" value={ scope.name } onChange={ (e) => this.handleInputChange(e) } />
											&nbsp;
											<span data-toggle="tooltip" data-placement="right" title={ scope.description }>{ scope.name }</span>
										</label>
									</div>
								)
							) : null }
						</div>
					</div>

					<div className="d-flex justify-content-between">
						<button type="button" className="btn btn-light" 
							onClick={ this.handleModalClose.bind(this, 'creationModal') }>Annuler</button>
						<button type="submit" className="btn btn-light">Créer le client</button>
					</div>
				</form>
			</Modal>
		);
		const clientDetailModal = (
			<Modal show={ this.state.togglers.detailModal } 
				title={<h4>Client { this.state.client.name }</h4>}
				onClose={ this.handleModalClose.bind(this, 'detailModal')}
			>
				<form role="form">
					<div className="form-group row">
						<label className="col-md-3 col-form-label">Nom :</label>
						<div className="col-md-9">
							<input type="text" disabled className="form-control" value={ this.state.client.name } />
							<span className="form-text text-muted">Le nom qui s'affichera pour vos utilisateurs.</span>
						</div>
					</div>

					<div className="form-group row">
						<label className="col-md-3 col-form-label">ID Asso :</label>
						<div className="col-md-9">
							<input type="number" min="0" disabled className="form-control" value={ this.state.client.asso_id } />
							<span className="form-text text-muted">L'ID de l'asso pour qui la clé est créee.</span>
						</div>
					</div>

					<div className="form-group row">
						<label className="col-md-3 col-form-label">Redirection :</label>
						<div className="col-md-9">
							<input type="text" className="form-control" disabled value={ this.state.client.redirect } />
							<span className="form-text text-muted">Adresse de redirection après authentification.</span>
						</div>
					</div>

					<div className="form-group row" v-if="form.scopes.length > 0">
						<label className="col-md-3 col-form-label">Scopes :</label>
						<div className="col-md-9" style={{ overflowY: 'auto', maxHeight: '300px' }}>
						{ this.state.client.scopes ? (
							this.state.client.scopes.map((scope, i) => 
								<span key={i} className="d-block mb-1">
									<code>{ scope }</code> : scopes[scope]
								</span>
							)
						) : (
							<span>Pas de scopes client.</span>
						)}
						</div>
					</div>
				</form>

				<div className="d-flex justify-content-end">
					<button type="button" className="btn btn-light" onClick={ this.handleModalClose.bind(this, 'detailModal') }>Fermer</button>
				</div>
			</Modal>
		);
		const clientEditionModal = (
			<Modal show={ this.state.togglers.editionModal } 
				title={<h4>Modifier un client</h4>}
				onClose={ this.handleModalClose.bind(this, 'editionModal')}
			>
				{ this.state.form.errors.length > 0 ? (
					<div className="alert alert-danger" v-if="form.errors.length > 0">
						<p className="mb-0"><strong>Erreur</strong></p>
						<br />
						Une erreur est survenue. Veuillez réessayer.
					</div>
				) : (<span></span>)}

				<form onSubmit={ (e) => this.handleSubmit('post', 'oauth/clients', e) }>
					<div className="form-group row">
						<label className="col-md-3 col-form-label">Nom :</label>

						<div className="col-md-9">
							<input id="create-client-name" type="text" className="form-control" name="name" value={ this.state.client.name } onChange={ (e) => this.handleInputChange(e) } />

							<span className="form-text text-muted">Le nom qui s'affichera pour vos utilisateurs.</span>
						</div>
					</div>

					<div className="form-group row">
						<label className="col-md-3 col-form-label">ID Asso :</label>

						<div className="col-md-9">
							<input name="asso_id" type="number" min="0" className="form-control" value={ this.state.client.asso_id } onChange={ (e) => this.handleInputChange(e) } />

							<span className="form-text text-muted">
								L'ID de l'asso pour qui la clé est créee.
							</span>
						</div>
					</div>

					<div className="form-group row">
						<label className="col-md-3 col-form-label">Redirection :</label>

						<div className="col-md-9">
							<input type="text" className="form-control" name="redirect" value={ this.state.client.redirect } onChange={ (e) => this.handleInputChange(e) } />

							<span className="form-text text-muted">
								Adresse de redirection après authentification.
							</span>
						</div>
					</div>

					<div className="form-group row">
						<label className="col-md-3 col-form-label">Scopes :</label>

						<div className="col-md-9">
							{ this.state.scopes.length > 0 ? (
								this.state.scopes.map((scope, i) => {
									var isChecked = false;
									if (this.state.client.scopes && this.state.client.scopes.indexOf(scope.name) != -1) 
										isChecked = true;

									return <div key={i} className="checkbox">
										<label>
											<input type="checkbox" name="scope" value={ scope.name } onChange={ (e) => this.handleInputChange(e) } checked={ isChecked ? "checked" : false } />
											&nbsp;
											<span data-toggle="tooltip" data-placement="right" title={ scope.description }>{ scope.name }</span>
										</label>
									</div>
								})
							) : null }
						</div>
					</div>

					<div className="text-right">
						<button type="button" className="btn btn-light" 
							onClick={ this.handleModalClose.bind(this, 'editionModal')}>Annuler</button>
						<button type="button" className="btn btn-danger mr-2"
							onClick={ this.deleteClient.bind(this, this.state.client) }>Supprimer</button>
						<button type="submit" className="btn btn-light">Modifier le client</button>
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
								<button className="btn btn-light" onClick={ this.toggle.bind(this, 'creationModal', true) }>
									Créer un client
								</button>
							</div>
						</div>

						{ this.state.clients.length > 0 ? (
							this.state.clients.map((client, i) => 
								<Client key={ i } client={client}
									editClient={this.editClient.bind(this)}
									viewClient={this.viewClient.bind(this)}
								/>)
						) : (
							<p className="mt-3 mb-0">Vous n'avez pas encore créé de client OAuth.</p>
						)}
					</div>
				</div>

				{ clientCreationModal }
				{ clientDetailModal }
				{ clientEditionModal }
			</div>
		);
	}

	/*
	|--------------------------------------------------------------------------
	|	API Calls
	|--------------------------------------------------------------------------
	*/

	getClients() {
		axios.get('/oauth/clients').then(response => {
			var clients = response.data;

			clients.map(function(client) {
				try {
					client.scopes = JSON.parse(client.scopes);

					if (client.scopes === null) {
						client.scopes = [];
					}
				}
				catch (error) {
					console.log(error);
					client.scopes = [];
				}
			});

			this.setState({ clients: clients });
		});
	}

	getTokens() {
		axios.get('/oauth/scopes').then(response => {
			var scopes = [];

			Object.keys(response.data).forEach(function(key) {
				if (key.startsWith('client')) {
					scopes.push({
						'name': key,
						'description': response.data[key]
					});
				}
			});

			this.setState({ scopes: scopes });
		});
	}

	/*
	|--------------------------------------------------------------------------
	|	Client Controllers
	|--------------------------------------------------------------------------
	| View, edit and delete
	*/

	viewClient(client) {
		this.setState({ client: client });
		this.toggle('detailModal', true);
	}

	editClient(client) {
		this.setState({ client: client });
		this.toggle('editionModal', true);
	}

	deleteClient(client) {
		this.handleModalClose('editionModal');
		axios.delete('/oauth/clients/' + this.state.client.id)
			.then(response => {
				this.getClients();
			});
	}

	/*
	|--------------------------------------------------------------------------
	|	Form Controllers
	|--------------------------------------------------------------------------
	*/

	handleInputChange(e) {
		const name = e.target.name;
		const value = e.target.value;  
		const oldForm = this.state.form;

		var form = {
			errors: oldForm.errors,
			name: oldForm.name,
			asso_id: oldForm.asso_id,
			redirect: oldForm.redirect,
			scopes: oldForm.scopes
		}

		if (name === "scope") {
			const i = form.scopes.indexOf(value);
			if (i == -1) {
				form.scopes.push(value);
			} else {
				form.scopes.splice(i, 1);
			}
		} else {
			form[name] = e.target.value;
		}

		this.setState({ form: form });
	}

	handleSubmit(method, url, e) {
		e.preventDefault();

		var form = this.state.form;
		form.errors = [];

		axios({ method: method, url: url, data: form })
			.then(response => {
				this.getClients();
				
				var form = {
					errors: [],
					name: '',
					asso_id: '',
					redirect: '',
					scopes: []
				}

				this.setState({ form: form });

				$("#creationModal").modal('toggle');
			})
			.catch(error => {                
				form.errors = ['Une erreur est survenue. Veuillez réessayer'];
				this.setState({ form: form });
			});
	}
}

export default Clients;