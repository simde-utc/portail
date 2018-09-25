import React from 'react';
import { connect } from 'react-redux';
import actions from '../redux/actions';
import { Card, CardBody, CardTitle, CardSubtitle, CardFooter, Button } from 'reactstrap';
import AspectRatio from 'react-aspect-ratio';

import Block from '../components/Block';

@connect(store => ({
	assos: store.getData('assos'),
	fetching: store.isFetching('assos'),
	fetched: store.isFetched('assos')
}))
class ScreensAssosList extends React.Component {
	componentWillMount() {
		this.props.dispatch(actions.assos.all())
	}

	getStage(assos) {
		return (
			<div className="d-md-inline-flex">
				<div className="bg-bde align-self-stretch" style={{ width: 100 }}>
					<span>BDE</span>
				</div>
				<div className="d-md-inline-flex flex-wrap">
					{ assos.map(asso => {
						var bg = 'bg-' + asso.login;

						if (asso.parent)
						bg += ' bg-' + asso.parent.login;

						return (
							<Card key={ asso.id } className={ "m-2 p-0 " + bg } style={{ width: 200 }} onClick={() => this.props.history.push('assos/' + asso.login)}>
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

	render() {
		return (
			<div className="container">
				<h1 className="title">Liste des associations</h1>
				<div className="content">
					<span className={"loader large" + (this.props.fetching ? ' active' : '') }></span>
					{ this.getStage(this.props.assos.filter(asso => {
						return asso.login === 'bde' || (asso.parent && asso.parent.login === 'bde')
					})) }
					{ this.getStage(this.props.assos.filter(asso => {
						return asso.login !== 'bde' && (!asso.parent || asso.parent.login !== 'bde')
					}).sort((asso1, asso2) => asso1.shortname > asso2.shortname)) }
				</div>
			</div>
		);
	}
}

export default ScreensAssosList;
