import React from 'react';
import { connect } from 'react-redux';
import { articlesActions } from '../redux/actions.js';

import ArticleList from './../components/Article/List.js';

@connect(store => ({
	articles: store.articles.data,
	fetching: store.articles.fetching,
	fetched: store.articles.fetched
}))
class ScreensHome extends React.Component {
	componentWillMount() {
			this.props.dispatch(articlesActions.getAll());
	}

  render() {
    return (
      <div className="container Home">
        <ArticleList articles={ this.props.articles } fetched={ this.props.fetched } fetching={ this.props.fetching } />
      </div>
    );
  }
}

export default ScreensHome;
