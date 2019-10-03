/**
 * List associations.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Matt Glorion <matt@glorion.fr>
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { Button, ButtonGroup, Input, InputGroup, InputGroupAddon } from 'reactstrap';
import { connect } from 'react-redux';
import { NavLink } from 'react-router-dom';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import actions from '../redux/actions';

import AssoCard from '../components/AssoCard';

@connect(store => ({
	assos: store.getData('assos'),
	fetching: store.isFetching('assos'),
	fetched: store.isFetched('assos'),
}))
class AssoListScreen extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			searchQuery: '',
			assosCemetaryActive: false,
		};
	}

	componentDidMount() {
		const { dispatch } = this.props;

		console.log('blob');

		dispatch(
			actions.assos.all({
				order: 'a-z',
				deleted: true,
			})
		);

		dispatch(actions.config({ title: 'Liste des associations' }));
	}

	onAssosCemetaryChange() {
		const { assosCemetaryActive } = this.state;
		this.setState({ assosCemetaryActive: !assosCemetaryActive });
	}

	getGrid(assos, filter) {
		const { assosCemetaryActive } = this.state;
		const regex = RegExp(
			filter
				.toLowerCase()
				.split('')
				.join('.*')
		);

		const filteredList = assos.filter(({ shortname, name, deleted_at }) => {
			if (!assosCemetaryActive) {
				return (
					(regex.test(shortname.toLowerCase()) || regex.test(name.toLowerCase())) &&
					deleted_at === null
				);
			}
			return (
				(regex.test(shortname.toLowerCase()) || regex.test(name.toLowerCase())) &&
				deleted_at != null
			);
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
								deleted={asso.deleted_at != null}
							/>
						</NavLink>
					);
				})}
			</div>
		);
	}

	populateGroupButtons() {
		const { assos } = this.props;
		const { asso_id } = this.state;
		const groups = {};

		assos.forEach(({ parent }) => {
			if (parent) {
				groups[parent.id] = parent;
			}
		});

		const buttons = Object.values(groups).sort((group1, group2) => {
			return group1.shortname > group2.shortname;
		});

		buttons.unshift({
			login: 'black',
			shortname: 'Tous',
		});

		return buttons.map(({ id, login, shortname }) => (
			<Button
				className={`bg-${login}`}
				key={id || 0}
				active={asso_id === id}
				onClick={() => this.setState({ asso_id: id })}
			>
				{shortname}
			</Button>
		));
	}

	isAssoInGroup({ id, parent }) {
		const { asso_id } = this.state;

		if (asso_id) {
			if (parent) {
				return parent.id === asso_id || asso_id === id;
			}

			return asso_id === id;
		}

		return true;
	}

	handleSearch(event) {
		this.setState({ searchQuery: event.target.value });
	}

	render() {
		const { fetching, assos } = this.props;
		const { searchQuery, assosCemetaryActive } = this.state;
		actions.config({ title: 'Liste des associations' });

		return (
			<div className="container">
				<h1 className="title">Liste des associations</h1>
				<div className="content">
					<div className="searchContainer">
						<div className="searchBar">
							<InputGroup>
								<InputGroupAddon addonType="prepend">
									<span style={{ margin: 'auto 10px', border: 'none' }}>
										<FontAwesomeIcon size="2x" icon="search" />
									</span>
								</InputGroupAddon>
								<Input
									name="Recherche"
									onChange={this.handleSearch.bind(this)}
									style={{ border: 'none' }}
								/>
							</InputGroup>
						</div>
						<div className="filterBar">
							<ButtonGroup>{this.populateGroupButtons()}</ButtonGroup>
						</div>
						<div>
							<input
								type="checkbox"
								name="assosCemetaryActive"
								className="form-check-input"
								checked={assosCemetaryActive === true}
								onChange={this.onAssosCemetaryChange.bind(this)}
							/>
							<label htmlFor="assosCemetaryActive" className="form-check-label">
								Cimetière des assos
							</label>
						</div>
					</div>
					<span className={`loader large${fetching ? ' active' : ''}`} />
					{!fetching && this.getGrid(assos.filter(this.isAssoInGroup.bind(this)), searchQuery)}
				</div>
			</div>
		);
	}
}

export default AssoListScreen;
