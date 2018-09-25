import React from 'react';
import { connect } from 'react-redux';
import AspectRatio from 'react-aspect-ratio';
import { Button } from 'reactstrap';

import actions from '../../redux/actions';

import ArticleList from '../../components/Article/List';

@connect((store, props) => ({
	user: store.getData('user', false),
	articles: store.getData(['assos', props.asso.id, 'articles']),
	fetched: store.isFetched(['assos', props.asso.id, 'articles']),
	fetching: store.isFetching(['assos', props.asso.id, 'articles']),
}))
class AssoMemberListScreen extends React.Component {
  componentWillMount() {
    if (this.props.asso.id) {
      this.loadAssosData(this.props.asso.id);
    }
  }

  componentWillReceiveProps(props) {
    if (this.props.asso.id !== props.asso.id) {
      this.loadAssosData(props.asso.id);
    }
  }

  loadAssosData(id) {
		this.props.dispatch(actions.definePath(['assos', this.props.asso.id, 'articles']).addValidStatus(416).articles().all({ owner: 'asso,' + this.props.asso.id }));
	}

	render() {
		return (
			<ArticleList articles={ this.props.articles } fetched={ this.props.fetched } fetching={ this.props.fetching } { ...this.props } />
		);
	}
}

export default AssoMemberListScreen;
