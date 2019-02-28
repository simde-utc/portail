import React from 'react';
import { Link } from 'react-router-dom';

import Img from '../Image';

import { getTime } from '../../utils';

const MAX_CONTENT_LENGTH = 80;

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
					<a className="text-info" onClick={this.toggleExpand}>
						Lire la suite
					</a>
				</p>
			);
		}

		return (
			<div className="Article row m-0 my-3 my-md-4 justify-content-start">
				<div
					className="col-12 col-md-3 col-xl-1 pt-1 pb-2 pb-md-0 pr-md-1 media"
					style={{ maxWidth: '250px' }}
				>
					<Img
						className="align-self-start img-fluid"
						images={[article.image, article.owned_by.image]}
						style={{ maxWidth: 100, marginRight: 10 }}
					/>
				</div>
				<div className="col-12 col-md-9 body">
					<h3 style={{ marginBottom: 0.5 }}>{article.title}</h3>
					<div>
						<Link className="text-secondary" to={`/assos/${article.owned_by.login}`}>
							<Img
								className="align-self-start img-fluid"
								image={article.owned_by.image}
								style={{ maxWidth: 20, marginRight: 5 }}
							/>
							{article.owned_by.shortname}
						</Link>
						<span style={{ marginLeft: 5 }} className="text-muted small">
							{getTime(article.created_at)}
						</span>
					</div>
					<div style={{ whiteSpace: 'pre-line' }}>{articleBody}</div>
				</div>
				{article.event ? 'Il y a un event associé !' : ''}
			</div>
		);
	}
}

export default Article;
