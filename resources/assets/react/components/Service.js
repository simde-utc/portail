import React from 'react';
import { Link } from 'react-router-dom';
import { Button } from 'reactstrap';

class Service extends React.Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<div className="Service row m-0 my-3 my-md-4 justify-content-start">
				<a className="col-12 col-md-9 body" href={ this.props.service.url }>
					<h3>{ this.props.service.name }</h3>
					{ this.props.service.description }
				</a>
				{ this.props.isFollowing ? (
					<Button className="m-1 btn btn-sm" color="warning" outline onClick={ this.props.unfollow }>
						Retirer des favoris
					</Button>
				) : (
					<Button className="m-1 btn btn-sm" color="primary" outline onClick={ this.props.follow }>
						Ajouter aux favoris
					</Button>
				)}
			</div>
		);
	}
}

export default Service;
