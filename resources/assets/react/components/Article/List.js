import React from 'react';
import { connect } from 'react-redux';
import { articlesActions } from '../../redux/actions.js';

import Article from './Article.js';

@connect(store => ({
	articles: store.articles.data,
	fetching: store.articles.fetching,
	fetched: store.articles.fetched
}))
class ArticleList extends React.Component {
	componentWillMount() {
		this.props.dispatch(articlesActions.getAll());
	}

	render() {
		return (
			<div className="container ArticleList">
				{ (this.props.fetched) ? (
					(this.props.articles.length > 0) ? (
						this.props.articles.map(article => (
							<Article key={ article.id } article={article} />
						))
					) : (
						<div>Aucun article n'est disponible pour le moment, revenez plus tard !.</div>
					)
				) : (
					<div>Chargement</div>
				)}
			</div>
		);
	}
}

export default ArticleList;