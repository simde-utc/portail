/**
 * Affichage des membres d'une association.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';
import Select from 'react-select';
import { find } from 'lodash';

import actions from '../../redux/actions';

import MemberList from '../../components/Member/DoubleList';

@connect((store, props) => ({
	config: store.config,
	user: store.getData('user', false),
	semesters: store.getData('semesters'),
	currentSemester: store.getData(['semesters', 'current']),
	members: store.getData(['assos', props.asso.id, 'members']),
	fetched: store.isFetched(['assos', props.asso.id, 'members']),
	fetching: store.isFetching(['assos', props.asso.id, 'members']),
	roles: store.getData(['assos', props.asso.id, 'roles']),
}))
class AssoMemberListScreen extends React.Component {
	constructor(props) {
		super(props);

		this.state = {};
		const { asso, currentSemester, dispatch } = props;

		if (asso.id) {
			this.loadAssosData(asso.id);
		}

		if (currentSemester) {
			this.state.semester_id = currentSemester.id;
		}

		dispatch(actions.config({ title: `${asso.shortname} - Membres` }));
	}

	componentWillMount() {
		const { asso, dispatch } = this.props;

		dispatch(actions.config({ title: `${asso.shortname} - Membres` }));
	}

	componentDidUpdate({ asso }) {
		const {
			asso: { id },
		} = this.props;

		if (asso.id !== id) {
			this.loadAssosData(id);
		}
	}

	static getSemesters(semesters) {
		return semesters.map(semester => ({
			value: semester.id,
			label: semester.name,
		}));
	}

	getBeforeTheCurrentSemester() {
		const { semesters, currentSemester } = this.props;
		let semester;

		for (const key in semesters) {
			const possibleSemester = semesters[key];

			if (possibleSemester.id === currentSemester.id) {
				return semester;
			}

			semester = possibleSemester;
		}

		return semester;
	}

	handleSemesterChange(value) {
		const {
			asso: { id },
		} = this.props;
		if (value && value.value) {
			this.setState({ semester_id: value.value }, () => {
				this.loadAssosData(id);
			});
		}
	}

	loadAssosData(id) {
		const { user, roles, dispatch } = this.props;
		const { semester_id } = this.state;

		actions
			.assos(id)
			.members(user.id)
			.get({ semester: this.getBeforeTheCurrentSemester().id })
			.payload.then(({ data }) => {
				const role = find(roles, role => role.id === data.pivot.role_id);

				if (role && role.type === 'president') {
					this.setState({
						lastRoleId: data.pivot.role_id,
					});
				}
			});

		dispatch(actions.assos(id).members.all({ semester: semester_id }));
	}

	render() {
		const { currentSemester, semesters, members, roles, fetched, fetching } = this.props;
		const { semester_id, lastRoleId } = this.state;
		const selectSemesters = AssoMemberListScreen.getSemesters(semesters);

		return (
			<div className="container">
				<div className="float-right" style={{ position: 'absolute', right: '15%', width: '85px' }}>
					Semestre:
					<Select
						onChange={this.handleSemesterChange.bind(this)}
						placeholder=""
						isSearchable
						options={selectSemesters}
						value={selectSemesters.filter(selectSemester => selectSemester.value === semester_id)}
					/>
				</div>
				<MemberList
					members={members}
					roles={roles}
					fetched={fetched}
					fetching={fetching}
					isCurrentSemester={semester_id === currentSemester.id}
					lastRoleId={lastRoleId}
					{...this.props}
				/>
			</div>
		);
	}
}

export default AssoMemberListScreen;
