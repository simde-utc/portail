import React from 'react';
import { connect } from 'react-redux';

import { Button } from 'reactstrap';
import { findIndex } from 'lodash';

import Member from './Member';

@connect(store => ({
	user: store.getData('user', false),
	currentSemester: store.getData(['semesters', 'current'], {}),
}))
class MemberList extends React.Component {
	getMemberBlocks(members, roles) {
		const { currentSemester, isMember, validateMember, leaveMember } = this.props;

		return members.map(member => {
			const props = {};

			if (member.pivot) {
				const index = findIndex(roles, ['id', member.pivot.role_id]);

				if (index !== -1) {
					props.description = roles[index].name;
				}
			}

			if (member.pivot.semester_id === currentSemester.id) {
				props.footer = (
					<div>
						{!(isMember && member.pivot.validated_by) && (
							<Button
								color="success"
								className="m-1"
								onClick={() => {
									validateMember && validateMember(member.id);
								}}
								outline
							>
								Valider
							</Button>
						)}
						<Button
							color="danger"
							className="m-1"
							onClick={() => {
								leaveMember && leaveMember(member.id);
							}}
							outline
						>
							Retirer
						</Button>
					</div>
				);
			}

			return <Member key={member.id} image={member.image} title={member.name} {...props} />;
		});
	}

	render() {
		const { title, members, roles } = this.props;

		return (
			<div className="container MemberList">
				<h1 className="title">{title}</h1>
				{members.length > 0 ? (
					<div className="d-flex justify-content-center flex-wrap mb-5">
						{this.getMemberBlocks(members, roles)}
					</div>
				) : (
					<p>Aucun membre</p>
				)}
			</div>
		);
	}
}

export default MemberList;
