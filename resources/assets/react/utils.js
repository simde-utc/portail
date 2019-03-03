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

// Dates relatives
export const getTime = time => {
	return moment(time).calendar(null, {
		sameDay: 'H:m',
		nextDay: '[Demain]',
		nextWeek: 'dddd',
		lastDay: '[Hier]',
		lastWeek: 'dddd',
		sameElse: 'D/M/YYYY',
	});
};

export const formatDate = date => {
	return moment(date).format();
};

export const colorFromBackground = hex => {
	hex = hex.slice(1);

	// convert 3-digit hex to 6-digits.
	if (hex.length === 3) {
		hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
	}

	const r = parseInt(hex.slice(0, 2), 16);
	const g = parseInt(hex.slice(2, 4), 16);
	const b = parseInt(hex.slice(4, 6), 16);

	// http://stackoverflow.com/a/3943023/112731
	return r * 0.299 + g * 0.587 + b * 0.114 > 186 ? '#000000' : '#FFFFFF';
};

export default {
	getTime,
	colorFromBackground,
};
