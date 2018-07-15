import React, { Component } from 'react';
import { connect } from 'react-redux';
import { assosActions } from '../redux/actions';

import AssoChildrenList from '../components/AssoChildrenList';

@connect(store => {
	return {
		assos: store.assos.data,
		fetching: store.assos.fetching,
		fetched: store.assos.fetched
	}
})
class AssosListScreen extends Component {

	componentWillMount() {
		this.props.dispatch(assosActions.getAll('?all'))
	}

	render() {
		// Construction de l'arbre des assos
		let assosTree = [];
		if (this.props.fetched)
			this.props.assos.forEach(asso => {
				if (asso.parent_id === null | asso.parent_id === 1) {
					// Ajout à la racine si BDE ou Poles
					assosTree.push({ ...asso, children: [] });
				} else {
					// Recherche du parent par recherche en largeur de l'arbre
					// TODO : cas où parent n'existe pas ?
					let nextParents = [];
					assosTree.forEach(parent => nextParents.push(parent));
					let parent;
					while(nextParents.length > 0) {
						parent = nextParents.pop();
						// On arrête si on a trouvé le parent
						if (parent.id === asso.parent_id)
							break;
						// Sinon on ajoute ses enfants à la liste de recherche 
						else
							nextParents = nextParents.concat(parent.children);
					}
					// Ajout en tant que fils des parents
					parent.children.push({ ...asso, children: [] });
				}
			})

		return (
			<div className="container">
				<h1 className="title">Liste des associations</h1>
				<span className={"loader large" + (this.props.fetching ? ' active' : '') }></span>

				<ul className="row list-row">
					{ assosTree.map(asso => (
						<AssoChildrenList key={asso.id} asso={asso} level={1} />
					)) }
					</ul>
			</div>
		);
	}
}

export default AssosListScreen;