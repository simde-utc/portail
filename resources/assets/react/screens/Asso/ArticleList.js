/**
 * Display assciation articles.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';
import { Button } from 'reactstrap';
import { NotificationManager } from 'react-notifications';

import actions from '../../redux/actions';

import ArticleForm from '../../components/Article/Form';
import ArticleList from '../../components/Article/List';

@connect((store, { asso: { id } }) => ({
	config: store.config,
	user: store.getData('user', false),
	articles: store.getData(['assos', id, 'articles']),
	fetched: store.isFetched(['assos', id, 'articles']),
	fetching: store.isFetching(['assos', id, 'articles']),
}))
class AssoArticleList extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			openModal: false,
		};
	}

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

	createArticle(data) {
		const { asso, dispatch } = this.props;

		data.owned_by_type = 'asso';
		data.owned_by_id = asso.id;

		const action = actions.articles.create({}, data);

		dispatch(action);

		return action.payload
			.then(() => {
				const { asso, dispatch } = this.props;

				dispatch(
					actions
						.definePath(['assos', asso.id, 'articles'])
						.addValidStatus(416)
						.articles()
						.all({ owner: `asso,${asso.id}` })
				);
				NotificationManager.success(
					"L'article a été publié avec succès",
					"Publication d'un article"
				);

				this.setState({ openModal: false });
			})
			.catch(() => {
				NotificationManager.error("L'article n'a pas pu être créé", "Publication d'un article");

				return Promise.reject();
			});
	}

	render() {
		const { articles, fetched, fetching } = this.props;
		const { openModal } = this.state;

		return (
			<div className="container">
				<ArticleForm
					post={this.createArticle.bind(this)}
					opened={openModal}
					closeModal={() => this.setState({ openModal: false })}
				/>
				<div className="d-flex flex-wrap-reverse align-items-center">
					<h1 className="title">Derniers articles</h1>
					<Button
						className="ml-auto"
						color="primary"
						outline
						onClick={() => this.setState({ openModal: true })}
					>
						Rédiger un article
					</Button>
				</div>
				<ArticleList articles={articles} fetched={fetched} fetching={fetching} {...this.props} />
			</div>
		);
	}
}

export default AssoArticleList;
