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
	componentDidMount() {
		const { dispatch } = this.props;
		dispatch(actions.config({ title: 'Mes Informations' }));
	}

	render() {
		const { user } = this.props;
		return <UserInfo info={user} />;
	}
}
export default InfoScreen;
