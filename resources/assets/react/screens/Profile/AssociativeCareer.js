/**
 * Display all associations of a given user.
 *
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';
import { NavLink } from 'react-router-dom';
import AssoCard from '../../components/AssoCard';

import actions from '../../redux/actions';

@connect(store => ({
	config: store.config,
	semesters: store.getData(['semesters']),
	semestersFetched: store.isFetched(['semesters']),
	roles: store.getData('roles'),
	rolesIsFetching: store.isFetching('roles'),
	rolesFetched: store.isFetched('roles'),
}))
class AssociativeCareerScreen extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			associativeSemesters: {},
		};
	}

	componentDidMount() {
		const {
			user: { name },
			dispatch,
			rolesFetched,
			semesters,
		} = this.props;
		const { associativeSemesters } = this.state;

		if (!rolesFetched) {
			dispatch(actions.roles.all());
		}

		dispatch(actions.config({ title: `${name} - Mon Parcours` }));

		semesters
			.slice()
			.reverse()
			.forEach(semester => {
				if (!associativeSemesters[semester.id]) {
					actions.user.assos.all({ semester: semester.id }).payload.then(({ data }) => {
						if (data.length > 0) {
							this.addNewAssociativeSemester(semester.id, data);
						}
					});
				}
			});
	}

	componentDidUpdate() {
		const {
			user: { name },
			dispatch,
			rolesFetched,
		} = this.props;

		if (!rolesFetched) {
			dispatch(actions.roles.all());
		}

		dispatch(actions.config({ title: `${name} - Mon Parcours` }));
	}

	addNewAssociativeSemester(semester_id, assos) {
		const { associativeSemesters } = this.state;
		const objectToAssign = {};
		objectToAssign[semester_id] = assos;
		this.setState({
			associativeSemesters: Object.assign({}, associativeSemesters, objectToAssign),
		});
	}

	render() {
		const { roles, rolesFetched, semesters, semestersFetched } = this.props;
		const { associativeSemesters } = this.state;

		return (
			<div className="ml-5">
				{rolesFetched &&
					semestersFetched &&
					Object.keys(associativeSemesters).map(semester_id => {
						const semester = semesters.find(semester => semester.id === semester_id);

						const assosBySemesterList = associativeSemesters[semester_id].slice().map(asso => {
							const roleName = roles.find(role => role.id === asso.pivot.role_id).name;

							return (
								<NavLink key={asso.id} to={`/assos/${asso.login}`}>
									<AssoCard
										key={asso.id}
										name={asso.name}
										shortname={`${asso.shortname} - ${roleName}`}
										image={asso.image}
										login={asso.parent ? asso.parent.login : asso.login}
									/>
								</NavLink>
							);
						});

						const title = assosBySemesterList ? (
							<h2 style={{ margin: 20 }}>Semestre {semester.name}</h2>
						) : null;

						return assosBySemesterList ? [title, ...assosBySemesterList] : null;
					})}
			</div>
		);
	}
}

export default AssociativeCareerScreen;
