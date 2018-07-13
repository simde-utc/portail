import React, { Component } from 'react';

import ArticlesList from './../components/Articles/List.js';

class HomeScreen extends Component {
    render() {
        return (
            <div className="Home">
                <ArticlesList />
            </div>
        );
    }
}

export default HomeScreen;