import React from 'react';
import AspectRatio from 'react-aspect-ratio';
import { Button } from 'reactstrap';
import ReactMarkdown from 'react-markdown';

class AssoHomeScreen extends React.Component {
	getFollowButton(isFollowing, isMember) {
		if (isFollowing && !isMember) {
			return (
				<Button className="m-1 btn btn-sm" color="danger" outline onClick={ this.props.unfollow }>
					Ne plus suivre
				</Button>
			)
		}
		else {
			if (isMember) {
				return (
					<Button className="m-1 btn btn-sm" outline disabled>
						Suivre
					</Button>
				)
			}
			else  {
				return (
					<Button className="m-1 btn btn-sm" color="primary" outline onClick={ this.props.follow }>
						Suivre
					</Button>
				)
			}
		}
	}

	getMemberButton(isMember, isFollowing, isWaiting) {
		if (isMember) {
			if (isWaiting) {
				return (
					<Button className="m-1 btn btn-sm" color="warning" outline onClick={() => { this.props.leave && this.props.leave(true) }}>
						En attente...
					</Button>
				)
			}
			else  {
				return (
					<Button className="m-1 btn btn-sm" color="danger" outline onClick={() => { this.props.leave && this.props.leave(false) }}>
						Quitter
					</Button>
				)
			}
		}
		else  {
			if (isFollowing) {
				return (
					<Button className="m-1 btn btn-sm" outline disabled>
						Rejoindre
					</Button>
				)
			}
			else  {
				return (
					<Button className="m-1 btn btn-sm btn" color="primary" outline onClick={ this.props.join }>
						Rejoindre
					</Button>
				)
			}
		}
	}

	render() {
		const asso = this.props.asso;

		let color = 'color-' + asso.login;

		if (asso.parent)
			color += ' color-' + asso.parent.login;

		return (
			<div className="container">
				{ (asso) ? (
					<div className="row">
						<div className="col-md-2 mt-3 px-1 d-flex flex-md-column">
							<AspectRatio className="mb-2" ratio="1">
								<img src="http://assos.utc.fr/larsen/style/img/logo-bde.jpg" style={{ width: "100%" }} />
							</AspectRatio>
							{ this.getFollowButton(this.props.userIsFollowing, this.props.userIsMember) }
							{ this.getMemberButton(this.props.userIsMember, this.props.userIsFollowing, this.props.userIsWaiting) }
						</div>
						<div className="col-md-8">
							<h1 className={ "title mb-1 " + color }>{ asso.shortname }</h1>
							<span className="d-block text-muted mb-4">{ asso.name }</span>
							<span>{ asso.type && asso.type.description }</span>
							<ReactMarkdown className="my-3" source={ asso.description } />
						</div>
						<div className="col-md-2"></div>
					</div>
				) : null }
			</div>
		);
	}
}

export default AssoHomeScreen;
