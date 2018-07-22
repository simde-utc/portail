import React, { Component } from 'react';
import { connect } from 'react-redux';
import { assosActions } from '../redux/actions';

@connect((store, props) => ({
	asso: store.assos.data.find(asso => asso.login == props.match.params.login),
	fetching: store.assos.fetching,
	fetched: store.assos.fetched,
}))
class AssoDetailScreen extends Component { 
	componentWillMount() {
		const login = this.props.match.params.login
		this.props.dispatch(assosActions.getOne(login));
	}

	render() {
		const { asso, fetching, fetched } = this.props;
		if (fetching || !fetched)
			return (<span className="loader huge active"></span>);

		let actions = [];
		if (asso.user) {
			if (asso.user.is_follower)
				actions.push(<button key="subscription" 
					className="my-1 btn btn-outline-warning">Se désabonner</button>)
			else
				actions.push(<button key="subscription" 
					className="my-1 btn btn-success">S'abonner</button>)
		}

		return (
			<div className="container">
				<h1 className="title">{ asso.name }</h1>

				{ actions.length > 0 && <div className="my-1 d-flex">{ actions }</div> }

				<span>{ asso.type.description }</span>
				<p className="my-3">{ asso.description }</p>
			</div>
		);
	}
};

export default AssoDetailScreen;
