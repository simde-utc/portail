import React, { Component } from 'react';
import { connect } from 'react-redux';
import { articlesActions } from '../../redux/actions.js';

import Article from './Article.js';

@connect(store => {
    return {
        articles: store.articles.data,
        fetching: store.articles.fetching,
        fetched: store.articles.fetched
    }
})
class ArticlesList extends Component {

    componentWillMount() {
        this.props.dispatch(articlesActions.getAll('?all'));
    }

    render() {
        console.log(this.props.articles);

        return (
            <div>
                { (this.props.fetched) ? (
                    this.props.articles.map(article => (
                        <Article article={article} />
                    ))
                ) : (
                    <div></div>
                )}
            </div>
        );
    }
}

export default ArticlesList;