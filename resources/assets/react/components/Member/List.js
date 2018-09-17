import React from 'react';
import { connect } from 'react-redux';

import { Card, CardBody, CardTitle, CardSubtitle, CardFooter, Button } from 'reactstrap';
import AspectRatio from 'react-aspect-ratio';
import { findIndex } from 'lodash';

@connect((store, props) => ({
	user: store.loggedUser.data
}))
class MemberList extends React.Component {
	getMemberBlocks(members, roles) {
		return members.map(member => {
			var className = "m-2 p-0";

			if (member.pivot) {
				let index = findIndex(roles, ['id', member.pivot.role_id]);

				if (index !== -1)
					var description = roles[index].name;
			}

			return (
				<Card key={ member.id } className={ className } style={{ width: 200 }}>
					<AspectRatio ratio="1" style={{ height: 200 }} className="d-flex justify-content-center">
						<img src={ member.image } alt="Photo non disponible" className="img-thumbnail" style={{ height: '100%' }} />
					</AspectRatio>
					<CardBody>
						<CardTitle>{ member.name }</CardTitle>
						<CardSubtitle>{ description }</CardSubtitle>
					</CardBody>
					{ this.props.isMember && (
						<CardFooter>
							{ member.pivot.validated_by ? (
								<Button color="danger" onClick={() => { this.props.leaveMember && this.props.leaveMember(member.id) }} outline>Retirer</Button>
							) : (
								<Button color="success" onClick={() => { this.props.validateMember && this.props.validateMember(member.id) }} outline>Valider</Button>
							)}
						</CardFooter>
					)}
				</Card>
			)
		})
	}

	render() {
		return (
			<div className="container MemberList">
				<h1 className="title">{ this.props.title }</h1>
				<div className="d-flex justify-content-center flex-wrap mb-5">
					{ this.getMemberBlocks(this.props.members, this.props.roles) }
				</div>
			</div>
		);
	}
}

export default MemberList;
