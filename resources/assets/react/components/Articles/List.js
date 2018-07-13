import React, { Component } from 'react';
import { connect } from 'react-redux';
import { articlesActions } from '../../actions.js';

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
        return (
            <div>
                { (this.props.fetched) ? (
                    this.props.articles.map(article => (
                        <div className="Article">
                            <h1>{ article.title }</h1>
                            <p>{ article.content }</p>
                        </div>
                    ))
                ) : (
                    <div></div>
                )}
            </div>
        );
    }
}

export default ArticlesList;