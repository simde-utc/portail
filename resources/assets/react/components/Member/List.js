import React from 'react';
import { connect } from 'react-redux';

import { Button } from 'reactstrap';
import { findIndex, orderBy } from 'lodash';

import Member from './Member';

@connect(store => ({
	user: store.getData('user', false),
	currentSemester: store.getData(['semesters', 'current'], {}),
}))
class MemberList extends React.Component {
	getMemberBlocks(members, roles) {
		const { currentSemester, isMember, isWaiting, validateMember, leaveMember } = this.props;

		members = orderBy(
			members.map(member => {
				const index = findIndex(roles, ['id', member.pivot.role_id]);

				if (index !== -1) {
					member.description = roles[index].name;
					member.position = roles[index].position;
				}

				return member;
			}),
			'position'
		);

		return members.map(member => {
			const props = {
				description: member.description,
			};

			if (member.pivot.semester_id === currentSemester.id) {
				props.footer = (
					<div>
						{(isMember || isWaiting) && !member.pivot.validated_by && (
							<Button
								color="success"
								className="m-1 font-weight-bold"
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
							className="m-1 font-weight-bold"
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
			<div className="container MemberList pb-4">
				<h1 className="title">{title}</h1>
				{members.length > 0 ? (
					<div className="d-flex justify-content-center flex-wrap mb-5">
						{this.getMemberBlocks(members, roles)}
					</div>
				) : (
					<div className="justify-content-center col-md-12 pt-6">
						<h5 style={{ textAlign: 'center' }}>Aucun membre</h5>
					</div>
				)}
			</div>
		);
	}
}

export default MemberList;
