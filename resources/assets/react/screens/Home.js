import React from 'react';

import ArticleList from './../components/Article/List.js';

class ScreensHome extends React.Component {
    render() {
        return (
            <div className="container Home">
                <ArticleList />
            </div>
        );
    }
}

export default ScreensHome;