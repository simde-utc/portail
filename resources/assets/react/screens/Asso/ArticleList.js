/**
 * Affichage des membres d'une association.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';

import actions from '../../redux/actions';

import ArticleList from '../../components/Article/List';

@connect((store, { asso: { id } }) => ({
	config: store.config,
	user: store.getData('user', false),
	articles: store.getData(['assos', id, 'articles']),
	fetched: store.isFetched(['assos', id, 'articles']),
	fetching: store.isFetching(['assos', id, 'articles']),
}))
class AssoArticleList extends React.Component {
	componentWillMount() {
		const {
			asso: { id, shortname },
			dispatch,
		} = this.props;

		if (id) {
			this.loadAssosData(id);
		}

		dispatch(actions.config({ title: `${shortname} - Articles` }));
	}

	componentDidUpdate({ asso }) {
		const {
			asso: { id, shortname },
			dispatch,
		} = this.props;

		if (asso.id !== id) {
			this.loadAssosData(id);

			dispatch(actions.config({ title: `${shortname} - Articles` }));
		}
	}

	loadAssosData(id) {
		const { dispatch } = this.props;

		dispatch(
			actions
				.definePath(['assos', id, 'articles'])
				.addValidStatus(416)
				.articles()
				.all({ owner: `asso,${id}` })
		);
	}

	render() {
		const { articles, fetched, fetching } = this.props;

		return (
			<div className="container">
				<ArticleList articles={articles} fetched={fetched} fetching={fetching} {...this.props} />
			</div>
		);
	}
}

export default AssoArticleList;
