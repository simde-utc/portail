import React from 'react';
import { connect } from 'react-redux';
import { assosActions } from '../redux/actions';

import AssoChildrenList from '../components/AssoChildrenList';

@connect(store => ({
	assos: store.assos.data,
	fetching: store.assos.fetching,
	fetched: store.assos.fetched
}))
class ScreensAssosList extends React.Component {
	componentWillMount() {
		this.props.dispatch(assosActions.getAll())
	}

	render() {
		// Construction de l'arbre des assos, supporte une profondeur quelconque
		let assosTree = [];
		if (this.props.fetched) {
			// Récupére les ids des assos de plus au niveau (BDE...), pour pouvoir
			let topLevelAssos = this.props.assos.filter(asso => (asso.parent_id == null)).map(asso => asso.id)
			this.props.assos.forEach(asso => {
				if (asso.parent_id == null || topLevelAssos.includes(asso.parent_id)) {
					// Ajout à la racine si BDE ou Poles
					assosTree.push({ ...asso, children: [] });
				} else {
					// Recherche du parent par recherche en largeur de l'arbre
					// TODO : cas où parent n'existe pas ?
					let potentialParents = [];
					assosTree.forEach(parent => potentialParents.push(parent));
					let parent = null;
					while(potentialParents.length > 0) {
						parent = potentialParents.pop();
						// On arrête si on a trouvé le parent
						if (parent.id === asso.parent_id)
							break;
						// Sinon on ajoute ses enfants à la liste de recherche
						else
							potentialParents = potentialParents.concat(parent.children);
					}
					// Ajout en tant que fils du parent
					if (parent != null)
						parent.children.push({ ...asso, children: [] });
				}
			})
		}

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

export default ScreensAssosList;
