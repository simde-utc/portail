import React from 'react';
import AspectRatio from 'react-aspect-ratio';
import { Button } from 'reactstrap';
import { connect } from 'react-redux';
import Select from 'react-select';

import actions from '../../redux/actions';

import MemberList from '../../components/Member/DoubleList';

@connect((store, props) => ({
	user: store.getData('user', false),
	semesters: store.getData('semesters'),
	currentSemester: store.getData(['semesters', 'current']),
	members: store.getData(['assos', props.asso.id, 'members']),
	fetched: store.isFetched(['assos', props.asso.id, 'members']),
	fetching: store.isFetching(['assos', props.asso.id, 'members']),
	roles: store.getData(['assos', props.asso.id, 'roles']),
}))
class AssoMemberListScreen extends React.Component {
	constructor() {
		super();

		this.state = {
			semester: undefined,
		};
	}

	componentDidMount() {
		if (this.props.asso.id) {
			this.loadAssosData(this.props.asso.id);
		}

		if (this.props.currentSemester) {
			this.setState(prevState => ({ ...prevState, semester: this.props.currentSemester.id }));
		}
	}

	componentWillReceiveProps(props) {
		if (this.props.asso.id !== props.asso.id) {
			this.loadAssosData(props.asso.id);
		}
	}

	loadAssosData(id) {
		this.props.dispatch(actions.assos(id).members.all({ semester: this.state.semester }));
	}

	handleSemesterChange(value) {
		if (value && value.value) {
			this.setState(prevState => ({ ...prevState, semester: value.value }), () => {
				this.loadAssosData(this.props.asso.id);
			});
		}
	}

	getSemesters(semesters) {
		return semesters.map(semester => ({
			value: semester.id,
			label: semester.name
		}));
	}

	render() {
		var semesters = this.getSemesters(this.props.semesters);

		return (
			<div>
				<div style={{ position: 'absolute', right: '5%' }}>
					Semestre:
					<Select
						onChange={ this.handleSemesterChange.bind(this) }
						placeholder=""
						isSearchable={ true }
						options={ semesters }
						value={ semesters.filter(semester => semester.value === this.state.semester) }
						/>
				</div>
				<MemberList members={ this.props.members } roles={ this.props.roles } fetched={ this.props.fetched } fetching={ this.props.fetching } { ...this.props } />
			</div>
		);
	}
}

export default AssoMemberListScreen;
// onInputChange={ this.handleSearchEvent.bind(this) }
