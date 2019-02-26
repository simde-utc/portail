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
		const { asso, currentSemester } = props;

		if (asso.id) {
			this.loadAssosData(asso.id);
		}

		if (currentSemester) {
			this.state.semester_id = currentSemester.id;
		}
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
		const { dispatch } = this.props;
		const { semester_id } = this.state;

		dispatch(actions.assos(id).members.all({ semester_id }));
	}

	render() {
		const { currentSemester, semesters, members, roles, fetched, fetching, asso, config } = this.props;
		const { semester_id } = this.state;
		const selectSemesters = AssoMemberListScreen.getSemesters(semesters);
		config.title = `${asso.shortname} - Membres`;

		return (
			<div>
				<div style={{ position: 'absolute', right: '5%', width: '85px' }}>
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
					{...this.props}
				/>
			</div>
		);
	}
}

export default AssoMemberListScreen;
