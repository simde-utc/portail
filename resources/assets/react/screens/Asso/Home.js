import React from 'react';
import AspectRatio from 'react-aspect-ratio';

class AssoHomeScreen extends React.Component {
	follow() {
		this.props.follow()
	}

	unfollow() {
		this.props.unfollow()
	}

	join(role_id) {
		this.props.join(role_id)
	}

	leave() {
		this.props.leave()
	}

	getFollowButton(isFollowing, isMember) {
		if (isFollowing && !isMember) {
			return (
				<button className="m-1 btn btn-sm btn-danger" onClick={ this.unfollow.bind(this) }>
					Ne plus suivre
				</button>
			)
		}
		else {
			if (isMember) {
				return (
					<button className="m-1 btn btn-sm btn-secondary" disabled>
						Suivre
					</button>
				)
			}
			else  {
				return (
					<button className="m-1 btn btn-sm btn-primary" onClick={ this.follow.bind(this) }>
						Suivre
					</button>
				)
			}
		}
	}

	getMemberButton(isMember, isFollowing, isWaiting) {
		if (isMember) {
			if (isWaiting) {
				return (
					<button className="m-1 btn btn-sm btn-warning" onClick={ this.leave.bind(this) } disabled>
						En attente...
					</button>
				)
			}
			else  {
				return (
					<button className="m-1 btn btn-sm btn-danger" onClick={ this.leave.bind(this) }>
						Se retirer
					</button>
				)
			}
		}
		else  {
			if (isFollowing) {
				return (
					<button className="m-1 btn btn-sm btn-secondary" disabled>
						Rejoindre
					</button>
				)
			}
			else  {
				return (
					<button className="m-1 btn btn-sm btn-primary" onClick={ this.join.bind(this) }>
						Rejoindre
					</button>
				)
			}
		}
	}

	render() {
		const asso = this.props.asso;
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
							<h1 className="title mb-1">{ asso.shortname }</h1>
							<span className="d-block text-muted mb-4">{ asso.name }</span>
							<span>{ asso.type && asso.type.description }</span>
							<p className="my-3">{ asso.description }</p>
						</div>
						<div className="col-md-2"></div>
					</div>
				) : null }
			</div>
		);
	}
}

export default AssoHomeScreen;
