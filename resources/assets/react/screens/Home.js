/**
 * Affichage principal
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';
import actions from '../redux/actions';

import ArticleList from '../components/Article/List';

@connect(store => ({
	config: store.config,
	articles: store.getData('articles'),
	fetching: store.isFetching('articles'),
	fetched: store.isFetched('articles'),
}))
class ScreensHome extends React.Component {
	componentWillMount() {
		const { dispatch } = this.props;

		dispatch(actions.articles().all());
	}

	render() {
		const { articles, fetched, fetching, config } = this.props;
		config.title = 'Flux';

		return (
			<div className="container Home">
				<ArticleList articles={articles} fetched={fetched} fetching={fetching} />
			</div>
		);
	}
}

export default ScreensHome;
