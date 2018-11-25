import React from 'react';
import { connect } from 'react-redux';
import actions from '../redux/actions';
import { Card, CardBody, CardTitle, CardSubtitle, CardFooter, Button } from 'reactstrap';
import AspectRatio from 'react-aspect-ratio';
import { sortBy } from 'lodash';

import Img from '../components/Image';

@connect(store => ({
	assos: store.getData('assos'),
	fetching: store.isFetching('assos'),
	fetched: store.isFetched('assos')
}))
class AssosListScreen extends React.Component {
	componentWillMount() {
		this.props.dispatch(actions.assos.all())
	}

	getStage(assos, parent) {
		return (
			<div key={ parent.id } className={ "mb-4 rounded border-" + parent.login } style={{ borderWidth: 2 }}>
				<div className={ "text-center h4 p-2 bg-" + parent.login }>
					{ parent.shortname } <small>{ parent.name }</small>
				</div>
				<div className="m-2" style={{ display: 'flex', overflowX: 'auto' }}>
					{ assos.map(asso => {
						var bg = 'bg-' + asso.login;

						if (asso.parent)
						bg += ' bg-' + asso.parent.login;

						return (
							<Card key={ asso.id } className={ "mr-3 p-0 " + bg } style={{ width: 200, minHeight: 250, flex: '0 0 auto' }} onClick={() => this.props.history.push('assos/' + asso.login)}>
								<AspectRatio ratio="1" style={{ maxHeight: 150 }} className="d-flex justify-content-center mt-2">
									<img src={ 'http://assos.utc.fr/larsen/style/img/logo-bde.jpg' } alt="Photo non disponible" className="img-thumbnail" style={{ height: '100%' }} />
								</AspectRatio>
								<CardBody>
									<CardTitle>{ asso.shortname }</CardTitle>
									<CardSubtitle>{ asso.name }</CardSubtitle>
								</CardBody>
							</Card>
						);
					})}
				</div>
			</div>
		)
	}

	getStages(assos) {
		var categories = {};

		assos.map(asso => {
			var id;

			if (asso.parent) {
				id = asso.parent.id;

				if (categories[id] === undefined) {
					categories[id] = {
						asso: asso.parent,
						assos: [asso],
					};
				}
				else {
					categories[id].assos.push(asso);
				}
			}
			else {
				id = asso.id;

				if (categories[id] === undefined) {
					categories[id] = {
						asso: asso,
						assos: [asso],
					};
				}
				else {
					categories[id].assos.push(asso);
				}
			}
		});

		return Object.keys(categories).map(key => this.getStage(sortBy(categories[key].assos, ['shortname']), categories[key].asso))
	}

	render() {
		return (
			<div className="container">
				<h1 className="title">Liste des associations</h1>
				<div className="content">
					<span className={"loader large" + (this.props.fetching ? ' active' : '') }></span>
					{ this.getStages(this.props.assos) }
				</div>
			</div>
		);
	}
}

export default AssosListScreen;
