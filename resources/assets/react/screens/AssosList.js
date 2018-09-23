import React from 'react';
import { connect } from 'react-redux';
import actions from '../redux/actions';

import Block from '../components/Block';

@connect(store => ({
	assos: store.assos.data,
	fetching: store.assos.fetching,
	fetched: store.assos.fetched
}))
class ScreensAssosList extends React.Component {
	componentWillMount() {
		this.props.dispatch(assosActions.getAll())
	}

	getStage(assos) {
		return (
			<div className="d-md-inline-flex flex-wrap">
				{ assos.map((asso, key) => {
					var bg = 'bg-' + asso.login;

					if (asso.parent)
					bg += ' bg-' + asso.parent.login;

					return (
						<Block key={ key }
							image="http://assos.utc.fr/larsen/style/img/logo-bde.jpg"
							text={ asso.shortname }
							class={ bg }
							style={{ width: 150 }}
							onClick={() => this.props.history.push('assos/' + asso.login)}
						/>
					)
				})}
			</div>
		)
	}

	render() {
		return (
			<div className="container">
				<h1 className="title">Liste des associations</h1>
				<span className={"loader large" + (this.props.fetching ? ' active' : '') }></span>
				{ this.getStage(this.props.assos.filter(asso => {
					return asso.login === 'bde' || (asso.parent && asso.parent.login === 'bde')
				})) }
				{ this.getStage(this.props.assos.filter(asso => {
					return asso.login !== 'bde' && (!asso.parent || asso.parent.login !== 'bde')
				}).sort((asso1, asso2) => asso1.shortname > asso2.shortname)) }
			</div>
		);
	}
}

export default ScreensAssosList;
