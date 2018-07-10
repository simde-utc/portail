import React, { Component } from 'react';

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
				createModal: false
			}
		}
	}

	componentDidMount() {
		this.getClients();
		this.getTokens();
	}

	toggle(key, force = null) {
		this.setState(prevState => {
			let newState = { togglers: {} };
			newState.togglers[key] = (force == null) ? !prevState.togglers[key] : force
			return newState;
		});
	}

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

	viewClient(client, e) {
		this.setState({ client: client });
		$("#viewModal").modal('toggle');
	}

	editClient(client, e) {
		this.setState({ client: client });
		$("#editModal").modal('toggle');
	}

	deleteClient(client, e) {
		axios.delete('/oauth/clients/' + this.state.client.id)
			.then(response => {
				this.getClients();
			});
	}

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

				$("#createModal").modal('toggle');
			})
			.catch(error => {                
				form.errors = ['Une erreur est survenue. Veuillez réessayer'];
				this.setState({ form: form });
			});
	}

	render() {
		return (
			<div>
				<div className="card drop-shadow mb-4">
					<div className="card-body">
						<div className="row">
							<div className="col-6">
								<h5>Clients OAuth</h5>
							</div>

							<div className="col-6 text-right">
								<button className="btn btn-light" onClick={ this.toggle.bind(this, 'createModal', true) }>
									Créer un client
								</button>
							</div>
						</div>

						{ this.state.clients.length > 0 ? (
							this.state.clients.map((client, i) => 
								<div key={i} className="row mt-3 mb-0">
									<div className="col-sm-3">
										<h6 className="d-block mb-2">{ client.name }</h6> 
										<button className="btn btn-light btn-sm mb-1 mr-1" onClick={ (e) => this.viewClient(client, e) }>
											Voir
										</button>
										<button className="btn btn-light btn-sm mb-1" onClick={ (e) => this.editClient(client, e) }>
											Modifier
										</button>
									</div>
									<table className="col-sm-9">
										<tbody>
											<tr>
												<th>ID Client</th>
												<td>{ client.id }</td>
											</tr>
											<tr>
												<th>ID Asso</th>
												<td>{ client.asso_id }</td>
											</tr>
											<tr>
												<th>Secret</th>
												<td><code>{ client.secret }</code></td>
											</tr>
										</tbody>
									</table>
								</div>
							)
						) : (
							<p className="mt-3 mb-0">Vous n'avez pas encore crée de client OAuth.</p>
						)}
					</div>
				</div>

				<div className="modal fade { this.state.togglers[createModal] ? 'show' : '' }" tabIndex="-1" role="dialog">
					<div className="modal-dialog modal-lg">
						<div className="modal-content">
							<div className="modal-body">
								<div className="row mb-3">
									<div className="col-6">
										<h4>Créer un client</h4>
									</div>
									<div className="col-6 text-right">
										<button id="hideModalBtn" type="button" className="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									</div>
								</div>

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
											<input id="create-client-name" type="text" className="form-control" name="name" onChange={ (e) => this.handleInputChange(e) } />

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

										<div className="col-md-9">
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
											) : (
												<span></span>
											)}
										</div>
									</div>

									<div className="row">
										<div className="col-6 text-left">
											<button type="button" className="btn btn-light" data-dismiss="modal">Annuler</button>
										</div>
										<div className="col-6 text-right">
											<button type="submit" className="btn btn-light">Créer le client</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<div className="modal fade" id="viewModal" tabIndex="-1" role="dialog">
					<div className="modal-dialog modal-lg">
						<div className="modal-content">
							<div className="modal-body">
								<div className="row mb-3">
									<div className="col-6">
										<h4>Voir</h4>
									</div>
									<div className="col-6 text-right">
										<button type="button" className="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									</div>
								</div>

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

										<div className="col-md-9">
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

								<div className="row">
									<div className="col-12 text-right">
										<button type="button" className="btn btn-light" data-dismiss="modal">Fermer</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div className="modal fade" id="editModal" tabIndex="-1" role="dialog">
					<div className="modal-dialog modal-lg">
						<div className="modal-content">
							<div className="modal-body">
								<div className="row mb-3">
									<div className="col-6">
										<h4>Modifier un client</h4>
									</div>
									<div className="col-6 text-right">
										<button type="button" className="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									</div>
								</div>

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
											) : (
												<span></span>
											)}
										</div>
									</div>

									<div className="row">
										<div className="col-6 text-left">
											<button type="button" className="btn btn-light" data-dismiss="modal">Annuler</button>
										</div>
										<div className="col-6 text-right">
											<button type="button" className="btn btn-danger mr-2" data-dismiss="modal" onClick={ (e) => this.deleteClient(this.state.client, e) }>Supprimer</button>
											<button type="submit" className="btn btn-light">Modifier le client</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		);
	}
}

export default Clients;