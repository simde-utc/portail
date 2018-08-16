import React, { Component } from 'react';

class ScreensAssoHome extends Component {
    render() {
        return (
            <div className="container">
                { (this.props.asso) ? (
                    <span>
                        <h1 className="title mb-2">{ this.props.asso.shortname }</h1>
                        <span className="d-block text-muted mb-4">{ this.props.asso.name }</span>
                        <span>{ this.props.asso.type.description }</span>
                        <p className="my-3">{ this.props.asso.description }</p>
                    </span>
                ) : <span></span> }
            </div>
        );
    }
}

export default ScreensAssoHome;
