/**
 * Main display.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Paco Pompeani <paco.pompeani@etu.utc.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';
import actions from '../redux/actions';

import ArticleList from '../components/Article/List';

const buttonStyle = {
	display: 'block',
	border: '1px solid black',
	borderRadius: '10px',
	margin: '0 auto 10px auto',
	width: '150px',
	textAlign: 'center',
	paddingTop: '10px',
	paddingBottom: '10px',
};

@connect()
class ScreensHome extends React.Component {
	constructor(props) {
		super(props);
		// fetched : are the first articles fetched ?
		// page : page counter
		// canAskMore : set to false when fetching or when there are no more articles)
		this.state = { page: 1, fetched: false, canAskMore: false };
		// I want this function to remember who is "this".
		// See https://stackoverflow.com/questions/33973648/react-this-is-undefined-inside-a-component-function
		this.moreArticles = this.moreArticles.bind(this);
	}

	componentDidMount() {
		const { dispatch } = this.props;

		actions
			.articles()
			.all()
			.payload.then(({ data }) =>
				this.setState(state => {
					const oldState = state || {};
					return {
						...oldState,
						fetched: true,
						canAskMore: true,
						articles: data,
					};
				})
			);
		dispatch(actions.config({ title: 'Flux' }));
	}

	moreArticles() {
		const { page } = this.state;
		const newPage = { page: page + 1 };

		// Do not display the button while fetching
		this.setState(oldState => ({ ...oldState, canAskMore: false }));

		// Fetch new articles
		actions
			.articles()
			.all(newPage)
			.payload.then(({ data }) =>
				this.setState(state => {
					return {
						...state,
						...newPage,
						canAskMore: true,
						articles: [...state.articles, ...Object.values(data)],
					};
				})
			)
			.catch(() => {
				// No more pages : Nothing to do, canAskMore is already set to false
			});
	}

	render() {
		const { articles = [], fetched = false, canAskMore } = this.state;

		return (
			<div className="container Home">
				<ArticleList articles={articles} fetched={fetched} />
				{fetched && canAskMore && (
					<a className="text-info" onClick={this.moreArticles} style={buttonStyle}>
						Plus d'articles !
					</a>
				)}
			</div>
		);
	}
}

export default ScreensHome;
