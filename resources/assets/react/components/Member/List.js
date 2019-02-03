import React from 'react';
import { connect } from 'react-redux';

import { Card, CardBody, CardTitle, CardSubtitle, CardFooter, Button } from 'reactstrap';
import AspectRatio from 'react-aspect-ratio';
import { findIndex } from 'lodash';

import Member from './Member';

@connect((store, props) => ({
	user: store.getData('user', false),
	currentSemester: store.getData(['semesters', 'current'], {})
}))
class MemberList extends React.Component {
	getMemberBlocks(members, roles) {
		return members.map(member => {
			if (member.pivot) {
				let index = findIndex(roles, ['id', member.pivot.role_id]);

				if (index !== -1)
					var description = roles[index].name;
			}

			var props = {description};

			if (member.pivot.semester_id === this.props.currentSemester.id) {
				props.footer = (
					<div>
						{ !(this.props.isMember && member.pivot.validated_by) && (
							<Button color="success" className="m-1" onClick={() => { this.props.validateMember && this.props.validateMember(member.id) }} outline>Valider</Button>
						)}
						<Button color="danger" className="m-1" onClick={() => { this.props.leaveMember && this.props.leaveMember(member.id) }} outline>Retirer</Button>
					</div>
				);
			}

			return (
				<Member key={ member.id } image={ member.image } title={ member.name } { ...props } />
			);
		})
	}

	render() {
		return (
			<div className="container MemberList">
				<h1 className="title">{ this.props.title }</h1>
				{ this.props.members.length > 0 ? (
					<div className="d-flex justify-content-center flex-wrap mb-5">
						{ this.getMemberBlocks(this.props.members, this.props.roles) }
					</div>
				) : (
					<p>Aucun membre</p>
				)}
			</div>
		);
	}
}

export default MemberList;
