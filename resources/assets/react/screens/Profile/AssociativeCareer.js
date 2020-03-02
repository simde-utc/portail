/**
 * Display all associations of a given user.
 *
 * @author Corentin Mercier <corentin@cmercier.fr>
 * @author Noé Amiot <noe.amiot@etu.utc.fr>
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
	rolesFetching: store.isFetching('roles'),
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
		const { dispatch, rolesFetched, semesters, rolesFetching } = this.props;
		const { associativeSemesters } = this.state;

		if (!rolesFetched && !rolesFetching) {
			dispatch(actions.roles.all());
		}

		semesters.forEach(semester => {
			if (associativeSemesters[semester.id] === undefined) {
				actions.user.assos
					.all({ cemetery: true, semester: semester.id, only: 'joined' })
					.payload.then(({ data }) => {
						if (data.length > 0) {
							this.addNewAssociativeSemester(semester.id, data);
						}
					});
			}
		});
	}

	addNewAssociativeSemester(semester_id, assos) {
		this.setState(prevState => {
			prevState.associativeSemesters[semester_id] = assos;
			return prevState;
		});
	}

	render() {
		const { roles, rolesFetched, semesters, semestersFetched } = this.props;
		const { associativeSemesters } = this.state;
		const associativeSemestersKeys = Object.keys(associativeSemesters);

		const orderedAssociativeSemestersKeys = [];
		for (let i = 0; i < semesters.length; i++)
			if (associativeSemestersKeys.indexOf(semesters[i].id) !== -1)
				orderedAssociativeSemestersKeys.push(semesters[i].id);

		if (associativeSemestersKeys.length) {
			return (
				<div className="ml-5 AssociativeCareer" key="test">
					{rolesFetched &&
						semestersFetched &&
						orderedAssociativeSemestersKeys.map(semester_id => {
							const semester = semesters.find(semester => semester.id === semester_id);

							const assosBySemesterList = associativeSemesters[semester_id].map(asso => {
								const role = roles.find(role => role.id === asso.pivot.role_id);

								return (
									<NavLink key={asso.id + semester.id} to={`/assos/${asso.login}`}>
										<AssoCard
											key={asso.id + semester.id}
											name={asso.name}
											shortname={asso.shortname}
											additionalInfo={role ? role.name : ''}
											image={asso.image}
											login={asso.parent ? asso.parent.login : asso.login}
											deleted={asso.in_cemetery_at != null}
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

		return <p className="text-center p-5">Vous n'avez pas encore été membre d'une association.</p>;
	}
}

export default AssociativeCareerScreen;
