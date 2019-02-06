import React from 'react';
import { connect } from 'react-redux';
import actions from '../redux/actions';
import { Card, CardBody, CardTitle, CardSubtitle, CardFooter, Button } from 'reactstrap';
import AspectRatio from 'react-aspect-ratio';
import { sortBy } from 'lodash';

import Img from '../components/Image';
import AssoCard from '../components/AssoCard';

@connect(store => ({
	assos: store.getData('assos'),
	fetching: store.isFetching('assos'),
	fetched: store.isFetched('assos')
}))
class AssosListScreen extends React.Component {
	componentWillMount() {
		this.props.dispatch(actions.assos.all({
			'order': 'a-z'
		}));
	}

	getStage(assos, parent) {
		return (
			<div className="pole-container" style={{ overflowX: 'auto' }}>
				<h2>{ parent.shortname }</h2>
				<small>{ parent.name }</small>
				<br/>

				{ assos.map(asso => {
					return <AssoCard key={ asso.id } name={ asso.name } shortname={ asso.shortname } image={ asso.image } login={ parent.login }/>;
				})}
			</div>
		);
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

		return sortBy(categories, category => category.asso.shortname)
			.map(({assos, asso}) => this.getStage(assos, asso))
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
