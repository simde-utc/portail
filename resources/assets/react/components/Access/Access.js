import React from 'react';
import { connect } from 'react-redux';
import { ListGroupItem, Button } from 'reactstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import Img from '../Image';

@connect(store => ({
	user: store.getData('user'),
}))
class Access extends React.Component {
	getState() {
		const { access } = this.props;

		if (access.confirmed_by) {
			if (access.validated_at) {
				if (access.validated) {
					return {
						color: 'success',
						text: 'Accès attribué',
					};
				}

				return {
					color: 'danger',
					text: 'Accès refusé',
				};
			}

			return {
				color: 'primary',
				text: 'En attente de validation',
			};
		}

		return {
			color: 'warning',
			text: 'En attente de confirmation',
		};
	}

	render() {
		const { user, access, canConfirm, cancel, confirm } = this.props;
		const { text, color } = this.getState();

		return (
			<ListGroupItem color={color}>
				<div className="container row">
					<div className="col-md-4">
						<Img
							image={access.member.image}
							style={{ height: '30px', paddingRight: '10px' }}
							unloader={<FontAwesomeIcon className="pr-2" size="2x" icon="user-alt" />}
						/>
						{access.member.name}
					</div>
					<div className="col-md-3">{access.access.name}</div>
					<div className="col-md-3">{text}</div>
					<div className="col-md-2">
						{!access.validated_at && (canConfirm || user.id === access.member.id) && (
							<Button
								className="float-right"
								onClick={() => {
									cancel(access);
								}}
							>
								<FontAwesomeIcon icon="times" />
							</Button>
						)}
						{!access.confirmed_by && canConfirm && (
							<Button
								className="float-right"
								onClick={() => {
									confirm(access);
								}}
							>
								<FontAwesomeIcon icon="check" />
							</Button>
						)}
					</div>
				</div>
			</ListGroupItem>
		);
	}
}

export default Access;
