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
import { NavLink } from 'react-router-dom';
import actions from '../redux/actions';

import AssoCard from '../components/AssoCard';

@connect(store => ({
	config: store.config,
	assos: store.getData('assos'),
	fetching: store.isFetching('assos'),
	fetched: store.isFetched('assos'),
}))
class AssoListScreen extends React.Component {
	constructor(props) {
		super(props);
		this.state = { searchQuery: '' };
	}

	componentWillMount() {
		const { dispatch } = this.props;

		dispatch(
			actions.assos.all({
				order: 'a-z',
			})
		);
	}

	static getGrid(assos, filter) {
		const regex = RegExp(
			filter
				.toLowerCase()
				.split('')
				.join('.*')
		);

		const filteredList = assos.filter(({ shortname, name }) => {
			return regex.test(shortname.toLowerCase()) || regex.test(name.toLowerCase());
		});

		return (
			<div className="assosContainer">
				{filteredList.map(asso => {
					return (
						<NavLink key={asso.id} to={`assos/${asso.login}`}>
							<AssoCard
								onClick={() => window.history.push(`assos/${asso.login}`)}
								key={asso.id}
								name={asso.name}
								shortname={asso.shortname}
								image={asso.image}
								login={asso.parent ? asso.parent.login : asso.login}
							/>
						</NavLink>
					);
				})}
			</div>
		);
	}

	handleSearch(event) {
		this.setState({ searchQuery: event.target.value });
	}

	render() {
		const { fetching, assos, config } = this.props;
		const { searchQuery } = this.state;
		config.title = 'Liste des associations';

		return (
			<div className="container">
				<h1 className="title">Liste des associations</h1>
				<div className="content">
					<div className="searchContainer">
						<div className="searchBar">
							<input
								className="searchInput"
								name="Recherche"
								onChange={this.handleSearch.bind(this)}
							/>
							<i className="fa fa-search" />
						</div>
					</div>
					<span className={`loader large${fetching ? ' active' : ''}`} />
					{!fetching && AssoListScreen.getGrid(assos, searchQuery)}
				</div>
			</div>
		);
	}
}

export default AssoListScreen;
