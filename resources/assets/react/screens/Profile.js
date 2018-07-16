import React, { Component } from 'react';
import { connect } from 'react-redux';
import loggedUserActions from '../redux/custom/loggedUser/actions';

@connect(store => ({
	user: store.loggedUser.data,
}))
class ProfileScreen extends Component {
	constructor(props) {
		super(props)
		this.state = {
			a: 'getDetails'
		}
	}

	componentWillMount() {
		this.props.dispatch(loggedUserActions.getInfo())
	}

	render() {
		const { user } = this.props;
		return (
			<div className="container">
				<h1 className="title">Mon profil</h1>
				<div>
					<pre className="pre-sdcrollable">{JSON.stringify(user, null, 2) }</pre>
				</div>
			</div>
		);
	}
}

export default ProfileScreen;