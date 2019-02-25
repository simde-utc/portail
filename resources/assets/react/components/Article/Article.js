import React from 'react';
import { Button } from 'reactstrap';
import { Link } from 'react-router-dom';

import Img from '../Image';

import { getTime } from '../../utils';

const MAX_CONTENT_LENGTH = 50;

class Article extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			expanded: false,
		};
		this.toggleExpand = this.toggleExpand.bind(this);
	}

	toggleExpand() {
		this.setState(prevState => ({ ...prevState, expanded: !prevState.expanded }));
	}

	render() {
		const { article } = this.props;
		const { expanded } = this.state;
		let articleBody = <p>{article.content}</p>;

		if (article.content.length > MAX_CONTENT_LENGTH && !expanded) {
			articleBody = (
				<p>
					{article.content.substring(0, MAX_CONTENT_LENGTH)}...&nbsp;
					<Button className="btn btn-link m-0 p-0 pb-1 blue" onClick={this.toggleExpand}>
						Lire la suite
					</Button>
				</p>
			);
		}

		return (
			<div className="Article row m-0 my-3 my-md-4 justify-content-start">
				<div
					className="col-12 col-md-3 col-xl-2 pt-1 pb-2 pb-md-0 pr-md-0 media"
					style={{ maxWidth: '250px' }}
				>
					<Img
						className="align-self-start img-fluid"
						image={[article.image, article.owned_by.image]}
						style={{ maxWidth: 100 }}
					/>
					<div className="media-body">
						<Link to={`/assos/${article.owned_by.login}`}>{article.owned_by.shortname}</Link>
						<span className="d-block text-muted small">{getTime(article.created_at)}</span>
					</div>
				</div>
				<div className="col-12 col-md-9 body">
					<h3>{article.title}</h3>
					<div style={{ whiteSpace: 'pre-line' }}>
						{articleBody}
					</div>
				</div>
				{article.event ? 'Il y a un event associé !' : ''}
			</div>
		);
	}
}

export default Article;
