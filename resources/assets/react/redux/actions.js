import { crudActions } from './utils';
import actionTypes from './types';

export const usersActions = new crudActions(actionTypes.users, 'users');
export const assosActions = new crudActions(actionTypes.assos, 'assos');
export const assoMembersActions = new crudActions(actionTypes.assoMembers, 'assos/{asso_id}/members');
export const articlesActions = new crudActions(actionTypes.articles, 'articles');
export const visibilitiesActions = new crudActions(actionTypes.visibilities, 'visibilities');
export const calendarsActions = new crudActions(actionTypes.calendars, 'calendars');
export const calendarEventsActions = new crudActions(actionTypes.calendarEvents, 'calendars/{calendar_id}/events');
export const contactsActions = new crudActions(actionTypes.contacts, '{resource}/{resource_id}/contacts');
export const rolesActions = new crudActions(actionTypes.roles, 'roles');
export const permissionsActions = new crudActions(actionTypes.permissions, 'permissions');
