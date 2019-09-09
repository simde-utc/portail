/**
 * Display all contributions of a given user.
 *
 * @author Amaury Guichard <amaury.guichard@etu.utc.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';
import moment from 'moment';
import ContributionsCard from '../../components/Profile/ContributionsCard';

import actions from '../../redux/actions';

@connect(store => ({
	config: store.config,
	contributions: store.getData(['user', 'contributions']),
	contributionsFetching: store.isFetching(['user', 'contributions']),
	contributionsFetched: store.isFetched(['user', 'contributions']),
}))
class Contributions extends React.Component {

	componentDidMount() {
		const { dispatch, contributionsFetched, contributionsFetching } = this.props;

    if(!contributionsFetched && !contributionsFetching){
      dispatch(actions.user.contributions.all());
    }
	}

	render() {
		const { contributions, contributionsFetched } = this.props;
		console.log(contributions);
		return (
			<div>
				{contributions.map(contribution => {
					return (
						<ContributionsCard
							key={contribution.start}
							semester1={contribution.semesters[0].name}
							semester2={contribution.semesters.length == 2 ? contribution.semesters[1].name : ''}
							amount={contribution.amount}
							start={moment(contribution.start, 'YYYY-MM-DD').format('Do MMMM YYYY')}
							end={moment(contribution.end, 'YYYY-MM-DD').format('Do MMMM YYYY')}
						/>
					);
				})}
			</div>
		);
	}
}
export default Contributions;
