/**
 * Display an article.
 *
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Romain Maliach-Auguste <r.maliach@live.fr>
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 */
import React from 'react';
import ReactMarkdown from 'react-markdown';
import { Link } from 'react-router-dom';

import Img from '../Image';

import { getTime } from '../../utils';

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
		const expandPossible = article.description !== article.content && !expanded;

		const articleBody = (
			<div style={{ whiteSpace: 'pre-line' }}>
				<ReactMarkdown
					source={
						expandPossible
							? `${article.description}...&nbsp;`
							: article.content
					}
					className="articleContent"
				/>
				{expandPossible && (
					<a className="text-info" onClick={this.toggleExpand}>
						Lire la suite
					</a>
				)}
			</div>
		);

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
					{articleBody}
				</div>
				{article.event ? 'Il y a un event associé !' : ''}
			</div>
		);
	}
}

export default Article;
