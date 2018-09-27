import React from 'react';
import { connect } from 'react-redux';
import actions from '../redux/actions.js';

import ArticleList from './../components/Article/List.js';

@connect(store => ({
	articles: store.getData('articles'),
	fetching: store.isFetching('articles'),
	fetched: store.isFetched('articles')
}))
class ScreensHome extends React.Component {
	componentWillMount() {
		this.props.dispatch(actions.articles().all());
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
