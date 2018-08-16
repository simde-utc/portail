import React, { Component } from 'react';
import { connect } from 'react-redux';
import { assosActions } from '../../redux/actions';
import loggedUserActions from '../../redux/custom/loggedUser/actions';
import { NavLink, Route, Switch } from 'react-router-dom';

import ScreensAssoHome from './Home.js';

/* TODO: Make it stateless & unconnected */
@connect((store, props) => ({
    asso: store.assos.data.find( asso => asso.login == props.match.params.login ),
    fetching: store.assos.fetching,
    fetched: store.assos.fetched,
    user: store.loggedUser.data,
}))
class ScreensAsso extends Component { 
    componentWillMount() {
        const login = this.props.match.params.login
        this.props.dispatch(assosActions.getOne(login));
        this.props.dispatch(loggedUserActions.getAssos());
    }

    render() {
        // var createArticleButton = <span></span>;
        // if (this.props.user.assos && this.props.user.assos.find( assos => assos.id === this.props.asso.id ))

        if (this.props.fetching || !this.props.fetched)
            return (<span className="loader huge active"></span>);

        let actions = [];
        // if (this.props.asso.user) {
        //     if (this.props.asso.user.is_follower)
        //         actions.push(<button key="subscription" 
        //             className="my-1 btn btn-outline-warning">Se désabonner</button>)
        //     else
        //         actions.push(<button key="subscription" 
        //             className="my-1 btn btn-success">S'abonner</button>)
        // }

        return (
            <div>
                <ul className="nav nav-tabs">
                    <li className="nav-item">
                        <NavLink className="nav-link" activeClassName="active" exact to={`${this.props.match.url}`}>Informations</NavLink>
                    </li>
                    <li className="nav-item">
                        <NavLink className="nav-link" activeClassName="active" to={`${this.props.match.url}/parcours_associatif`}>Parcours Associatif</NavLink>
                    </li>
                </ul>
                <div className="container">
                    <Switch>
                        <Route path={`${this.props.match.url}`} exact render={ () => { 
                                return this.props.asso ? <ScreensAssoHome asso={ this.props.asso } /> : <div></div>
                            }} />
                        <Route path={`${this.props.match.url}/parcours_associatif`} render={
                            () => <div></div>
                        } />
                    </Switch>
                </div>
            </div>
        );
    }
};

export default ScreensAsso;
