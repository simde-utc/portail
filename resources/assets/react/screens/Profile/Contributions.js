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

		dispatch(actions.config({ title: 'Mes Cotisations' }));

		if (!contributionsFetched && !contributionsFetching) {
			dispatch(actions.user.contributions.all());
		}
	}

	render() {
		const { contributions, contributionsFetched } = this.props;
		if (contributions.length > 0) {
			return (
				<div>
					{contributionsFetched &&
						contributions.reverse().map(contribution => {
							return (
								<ContributionsCard
									key={contribution.start}
									semesters={contribution.semesters}
									amount={contribution.amount}
									start={moment(contribution.start, 'YYYY-MM-DD')}
									end={moment(contribution.end, 'YYYY-MM-DD')}
								/>
							);
						})}
				</div>
			);
		}

		return <p className="text-center p-5">Vous n'avez pas encore cotisé au bde.</p>;
	}
}
export default Contributions;
