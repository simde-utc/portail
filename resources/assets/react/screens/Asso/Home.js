import React from 'react';
import { connect } from 'react-redux';
import AspectRatio from 'react-aspect-ratio';
import { Button } from 'reactstrap';
import ReactMarkdown from 'react-markdown';

import actions from '../../redux/actions';

import ContactList from '../../components/Contact/List';
import Img from '../../components/Image';

@connect((store, props) => ({
	isAuthenticated: store.isFetched('user'),
	contacts: store.getData(['assos', props.asso.id, 'contacts']),
	contactsFailed: store.hasFailed(['assos', props.asso.id, 'contacts']),
	roles: store.getData(['assos', props.asso.id, 'roles']),
}))
class AssoHomeScreen extends React.Component {
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
		this.props.dispatch(actions.assos(id).contacts.all());
	}

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
								<Img image={ asso.image } style={{ width: "100%" }} />
							</AspectRatio>
							{ this.props.isAuthenticated && this.getFollowButton(this.props.userIsFollowing, this.props.userIsMember) }
							{ this.props.isAuthenticated && this.getMemberButton(this.props.userIsMember, this.props.userIsFollowing, this.props.userIsWaiting) }
						</div>
						<div className="col-md-8">
							<h1 className={ "title " + color }>{ asso.shortname } <small className="text-muted h4">{ asso.name }</small></h1>
							<span className="mt-4">{ asso.type && asso.type.description }</span>
							<ReactMarkdown className="my-3 text-justify" source={ asso.description } />
							<ContactList className="mt-4" contacts={ this.props.contacts } authorized={ !this.props.contactsFailed } />
						</div>
					</div>
				) : null }
			</div>
		);
	}
}

export default AssoHomeScreen;
