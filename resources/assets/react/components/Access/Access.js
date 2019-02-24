import React from 'react';
import { connect } from 'react-redux';
import { ListGroupItem, Button } from 'reactstrap';
import { Link } from 'react-router-dom';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import Img from '../Image';

import { getTime } from '../../utils';

@connect(store => ({
	user: store.getData('user')
}))
class Access extends React.Component {
	getStateColor() {
		const { access } = this.props;

		if (access.confirmed_by) {
			if (access.validated_at) {
				return access.validated ? 'success' : 'danger';
			} else {
				return 'primary';
			}
		} else {
			return 'warning';
		}
	}

	getStateText() {
		const { access } = this.props;

		if (access.confirmed_by) {
			if (access.validated_at) {
				return access.validated ? 'Accès attribué' : 'Accès refusé';
			} else {
				return 'En attente de validation';
			}
		} else {
			return 'En attende de confirmation';
		}
	}

	getValidationButton() {
		const { access, canConfirm } = this.props;

		if (!access.confirmed_by && canConfirm) {
			return (
				<Button className="float-right">
					<FontAwesomeIcon icon="check" />
				</Button>
			);
		}
	}

	getRemoveButton() {
		const { user, access, canConfirm } = this.props;

		if (!access.validated_at && (canConfirm || user.id === access.member.id)) {
			return (
				<Button className="float-right">
					<FontAwesomeIcon icon="times" />
				</Button>
			);
		}
	}

	render() {
		const { access } = this.props;

		return (
			<ListGroupItem color={ this.getStateColor() }>
				<div className="container row">
					<div className="col-md-4">
						<Img images={ access.member.image } style={{ height: '30px', paddingRight: '10px' }} />
						{ access.member.name }
					</div>
					<div className="col-md-3">
					{ access.access.name }
					</div>
					<div className="col-md-3">
					{ this.getStateText() }
					</div>
					<div className="col-md-2">
					{ this.getRemoveButton() }
					{ this.getValidationButton() }
					</div>
				</div>
			</ListGroupItem>
		);
	}
}

export default Access;
