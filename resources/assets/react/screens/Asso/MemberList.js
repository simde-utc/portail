import React from 'react';
import AspectRatio from 'react-aspect-ratio';
import { Button } from 'reactstrap';
import { connect } from 'react-redux';

import actions from '../../redux/actions';

import MemberList from '../../components/Member/DoubleList';

@connect((store, props) => ({
	user: store.getData('user', false),
	members: store.getData(['assos', props.asso.id, 'members']),
	roles: store.getData(['assos', props.asso.id, 'roles']),
}))
class AssoMemberListScreen extends React.Component {
  componentWillMount() {
    if (this.props.asso.id) {
      this.loadAssosData(this.props.asso.id);
    }
  }

  componentWillReceiveProps(props) {
    if (this.props.asso.id !== props.asso.id) {
      this.loadAssosData(props.asso.id);
    }
  }

  loadAssosData(id) {
		this.props.dispatch(actions.assos(this.props.asso.id).members.all());
	}

	render() {
		return (
			<MemberList members={ this.props.members } roles={ this.props.roles } { ...this.props } />
		);
	}
}

export default AssoMemberListScreen;
