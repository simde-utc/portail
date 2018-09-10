import React from 'react';
import { Link } from 'react-router-dom';

import { getTime } from '../../utils.js';

class Article extends React.Component {
	constructor() {
		super();
		this.state = {
			expanded: false
		}
		this.toggleExpand = this.toggleExpand.bind(this)
	}

	toggleExpand(e) {
		this.setState(prev => ({ ...prev, expanded: !this.state.expanded }));
	}

	render() {
		const article = this.props.article;
		var articleBody = (<p>{ article.content }</p>);
		const MAX_CONTENT_LENGTH = 50;

		if (article.content.length > MAX_CONTENT_LENGTH && !this.state.expanded) {
			articleBody = (
				<p>
					{ article.content.substring(0, MAX_CONTENT_LENGTH) }...&nbsp;
					<button className="btn btn-link m-0 p-0 pb-1 blue" onClick={this.toggleExpand}>Lire la suite</button>
				</p>
			);
		}

		return (
			<div className="Article row m-0 my-3 my-md-4 justify-content-start">
				<div className="col-12 col-md-3 col-xl-2 pt-1 pb-2 pb-md-0 pr-md-0 media" style={{ maxWidth: '250px' }}>
					<img className="align-self-start img-fluid rounded-circle mr-2" src="http://via.placeholder.com/50x50" />
					<div className="media-body">
						<Link to={ "/assos/" + this.props.article.owned_by.login }>{ article.owned_by.shortname }</Link>
						<span className="d-block text-muted small">{ getTime(article.created_at) }</span>
					</div>
				</div>
				<div className="col-12 col-md-9 body">
					<h3>{ article.title }</h3>
					{ articleBody }
				</div>
			</div>
		);
	}
}

export default Article;