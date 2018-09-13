import { createCrudTypes } from './utils';

// CRUD Action Types
export const usersActionTypes = createCrudTypes('USERS');
export const assosActionTypes = createCrudTypes('ASSOS');
export const assoMembersActionTypes = createCrudTypes('ASSO_MEMBERS');
export const articlesActionTypes = createCrudTypes('ARTICLES');
export const visibilitiesActionTypes = createCrudTypes('VISIBILITIES');
export const calendarsActionTypes = createCrudTypes('CALENDARS');
export const calendarEventsActionTypes = createCrudTypes('CALENDAR_EVENTS');

export default {
	users: usersActionTypes,
	assos: assosActionTypes,
	assoMembers: assoMembersActionTypes,
  articles: articlesActionTypes,
	visibilities: visibilitiesActionTypes,
	calendars: calendarsActionTypes,
	calendarEvents: calendarEventsActionTypes,
}
