import React from 'react';

import ArticleList from './../components/Article/List.js';

class ScreensHome extends React.Component {
    render() {
        return (
            <div className="Home">
                <ArticleList />
            </div>
        );
    }
}

export default ScreensHome;