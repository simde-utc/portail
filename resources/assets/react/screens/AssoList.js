/**
 * Gestion des tokens
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Matt Glorion <matt@glorion.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';
import { sortBy } from 'lodash';
import { NavLink } from 'react-router-dom';
import actions from '../redux/actions';

import AssoCard from '../components/AssoCard';

@connect(store => ({
	assos: store.getData('assos'),
	fetching: store.isFetching('assos'),
	fetched: store.isFetched('assos'),
}))
class AssoListScreen extends React.Component {
	componentWillMount() {
		const { dispatch } = this.props;

		dispatch(
			actions.assos.all({
				order: 'a-z',
			})
		);
	}

	static getStage(assos, parent) {
		return (
			<div key={parent.id} className="pole-container">
				<h2>{parent.shortname}</h2>
				<small>{parent.name}</small>
				<div>
					{assos.map(asso => {
						return (
							<NavLink key={asso.id} to={`assos/${asso.login}`}>
								<AssoCard
									onClick={() => window.history.push(`assos/${asso.login}`)}
									key={asso.id}
									name={asso.name}
									shortname={asso.shortname}
									image={asso.image}
									login={parent.login}
								/>
							</NavLink>
						);
					})}
				</div>
			</div>
		);
	}

	static getStages(assos) {
		const categories = assos.reduce((acc, asso) => {
			if (asso.parent) {
				const { id } = asso.parent;

				if (acc[id] === undefined) {
					acc[id] = {
						asso: asso.parent,
						assos: [asso],
					};
				} else {
					acc[id].assos.push(asso);
				}
			} else {
				const { id } = asso;

				if (acc[id]) {
					acc[id].assos.push(asso);
				} else {
					acc[id] = {
						asso,
						assos: [asso],
					};
				}
			}

			return acc;
		}, {});

		return sortBy(categories, category => category.asso.shortname).map(({ assos, asso }) =>
			AssoListScreen.getStage(assos, asso)
		);
	}

	render() {
		const { fetching, assos } = this.props;

		return (
			<div className="container">
				<h1 className="title">Liste des associations</h1>
				<div className="content">
					<span className={`loader large${fetching ? ' active' : ''}`} />
					{AssoListScreen.getStages(assos)}
				</div>
			</div>
		);
	}
}

export default AssoListScreen;
