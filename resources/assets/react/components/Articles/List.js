import React, { Component } from 'react';
import { connect } from 'react-redux';
import { fetchArticles } from './../../actions/articles.js';

@connect(store => {
    return {
        articles: store.articles.articles
    }
})
class ArticlesList extends Component {

    componentWillMount() {
        this.props.dispatch(fetchArticles())
    }

    render() {
        return (
            <div>
                {this.props.articles.map(article => (
                    <div className="Article">
                        <h1>{ article.title }</h1>
                        <p>{ article.content }</p>
                    </div>
                ))}
            </div>
        );
    }
}

export default ArticlesList;