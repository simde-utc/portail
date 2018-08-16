import React, { Component } from 'react';

import ArticlesList from './../components/Articles/List.js';

class ScreensHome extends Component {
    render() {
        return (
            <div className="Home">
                <ArticlesList />
            </div>
        );
    }
}

export default ScreensHome;