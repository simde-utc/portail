import moment from 'moment';
import 'moment/locale/fr';

/* Dates relatives */
export function getTime(time) {
    moment.locale('fr');
    return moment(time).calendar(null, {
        sameDay: 'H:m',
        nextDay: '[Demain]',
        nextWeek: 'dddd',
        lastDay: '[Hier]',
        lastWeek: 'dddd',
        sameElse: 'D/M/YYYY'
    });
}