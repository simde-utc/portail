/**
 * Display user's personnal data.
 *
 * @author Paco Pompeani <paco.pompeani@etu.utc.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';

import actions from '../../redux/actions';
import UserInfo from '../../components/Profile/UserInfo';

@connect(store => ({
	user: store.getData('user'),
}))
class InfoScreen extends React.Component {
	constructor(props) {
		super(props);
		this.state = { data: {} };
	}

	componentDidMount() {
		actions.user.types
			.description()
			.all()
			.payload.then(({ data }) => this.setState(() => ({ data, fetched: true })));
	}

	render() {
		const { user } = this.props;
		const { data, fetched } = this.state;
		return <UserInfo info={user} typeNames={data} typeNamesFetched={fetched} />;
	}
}
export default InfoScreen;
