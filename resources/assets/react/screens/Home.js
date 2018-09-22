import React from 'react';
import { connect } from 'react-redux';
import actions from '../redux/api.js';

import ArticleList from './../components/Article/List.js';

@connect(store => ({
		articles: store.getData('articles'),
		fetching: store.isFetching('articles'),
		fetched: store.isFetched('articles')
}))
class ScreensHome extends React.Component {
	componentWillMount() {
		this.props.dispatch(actions().articles().all());
		this.props.dispatch(actions().articles('545f7400-ba48-11e8-b52a-670c9bfb310f').actions().all());
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
