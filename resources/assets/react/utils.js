/**
 * Outils pratiques comme la conversion de dates
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import moment from 'moment';
import 'moment/locale/fr';

// Dates relatives
export function getTime(time) {
	moment.locale('fr');
	return moment(time).calendar(null, {
		sameDay: 'H:m',
		nextDay: '[Demain]',
		nextWeek: 'dddd',
		lastDay: '[Hier]',
		lastWeek: 'dddd',
		sameElse: 'D/M/YYYY',
	});
}

export default {
	getTime,
};
