import React from 'react';

import Article from './Article.js';

class ArticleList extends React.Component {
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
