import React from 'react';
import {connect} from 'react-redux';
import actions from '../redux/actions';
import {sortBy} from 'lodash';
import { NavLink } from 'react-router-dom';

import AssoCard from '../components/AssoCard';

@connect(store => ({
    assos: store.getData('assos'),
    fetching: store.isFetching('assos'),
    fetched: store.isFetched('assos')
}))
class AssosListScreen extends React.Component {
    componentWillMount() {
        this.props.dispatch(actions.assos.all({
            'order': 'a-z'
        }));
    }

    getStage(assos, parent) {
        return (
            <div className="pole-container">
                <h2>{parent.shortname}</h2>
                <small>{parent.name}</small>
                <div>
                    {
                        assos.map(asso => {
                            return <NavLink to={'assos/' + asso.login}>
                                        <AssoCard onClick={() => history.push('assos/' + asso.login)}
                                                      key={asso.id} name={asso.name} shortname={asso.shortname}
                                                      image={asso.image} login={parent.login}/>
                                    </NavLink>;
                        })
                    }
                </div>
            </div>
        );
    }

    getStages(assos) {
        let categories = {};

        assos.map(asso => {
            var id;

            if (asso.parent) {
                id = asso.parent.id;

                if (categories[id] === undefined) {
                    categories[id] = {
                        asso: asso.parent,
                        assos: [asso],
                    };
                } else {
                    categories[id].assos.push(asso);
                }
            } else {
                id = asso.id;

                if (categories[id] === undefined) {
                    categories[id] = {
                        asso: asso,
                        assos: [asso],
                    };
                } else {
                    categories[id].assos.push(asso);
                }
            }
        });

        return sortBy(categories, category => category.asso.shortname)
            .map(({assos, asso}) => this.getStage(assos, asso))
    }

    render() {
        return (
            <div className="container">
                <h1 className="title">Liste des associations</h1>
                <div className="content">
                    <span className={"loader large" + (this.props.fetching ? ' active' : '')}/>
                    {this.getStages(this.props.assos)}
                </div>
            </div>
        );
    }
}

export default AssosListScreen;
