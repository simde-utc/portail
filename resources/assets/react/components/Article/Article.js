import React, { Component } from 'react';
import { Link } from 'react-router-dom';

import { getTime } from '../../utils.js';

class Article extends Component {
    constructor() {
        super();
        this.state = {
            text: "",
            expanded: false
        }
    }

    componentDidMount() {
        this.setState({ text: this.props.article.content });
    }

    toggleExpand(e) {
        this.setState({ expanded: !this.state.expanded });
    }

    render() {
        const content = this.props.article.content;
        var articleBody = <p>{ content }</p>

        if (content.length > 200 && !this.state.expanded) {
            articleBody = (
                <p>
                    { content.substring(0, 200) }... &nbsp;
                    <button className="btn btn-link m-0 p-0 mb-1" onClick={ (e) => this.toggleExpand(e) }>
                        Lire la suite
                    </button>
                </p>
            );
        }

        return (
            <div className="Article row m-0 my-3 my-md-5">
                <div className="col-md-2 mb-3 mb-md-0">
                    <div className="ml-0 ml-md-4 ml-xl-5 media mt-1">
                        <img className="align-self-start img-fluid rounded-circle mr-2" src="http://via.placeholder.com/50x50" />
                        <div className="media-body">
                            <Link to={ "/assos/" + this.props.article.owned_by.login }>{ this.props.article.owned_by.shortname }</Link>
                            <span className="d-block text-muted small">{ getTime(this.props.article.created_at) }</span>
                        </div>
                    </div>
                </div>
                <div className="col-md-8 body mx-auto">
                    <h3>{ this.props.article.title }</h3>
                    { articleBody }
                </div>
                <div className="col-md-2 right">
                </div>
            </div>
        );
    }
}

export default Article;