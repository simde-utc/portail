import React from 'react';

class ScreensAssoHome extends React.Component {
    render() {
        return (
            <div className="container">
                { (this.props.asso) ? (
                    <div className="row">
                        <div className="col-md-2 mt-5">
                            <button className="btn btn-sm btn-secondary mr-2">Suivre</button>
                            <button className="btn btn-sm btn-secondary">Rejoindre</button>
                        </div>
                        <div className="col-md-8">
                            <h1 className="title mb-2">{ this.props.asso.shortname }</h1>
                            <span className="d-block text-muted mb-4">{ this.props.asso.name }</span>
                            <span>{ this.props.asso.type.description }</span>
                            <p className="my-3">{ this.props.asso.description }</p>
                        </div>
                        <div className="col-md-2"></div>
                    </div>
                ) : <span></span> }
            </div>
        );
    }
}

export default ScreensAssoHome;
