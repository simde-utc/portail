import React, { Component } from 'react';

import ArticleList from './../components/Article/List.js';

class ScreensHome extends Component {
    render() {
        return (
            <div className="Home">
                <ArticleList />
            </div>
        );
    }
}

export default ScreensHome;