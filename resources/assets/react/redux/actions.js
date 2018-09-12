import { crudActions } from './utils';
import actionTypes from './types';

export const usersActions = new crudActions(actionTypes.users, 'users');
export const assosActions = new crudActions(actionTypes.assos, 'assos');
export const assoMembersActions = new crudActions(actionTypes.assoMembers, 'assos/{asso_id}/members');
export const articlesActions = new crudActions(actionTypes.articles, 'articles');
export const visibilitiesActions = new crudActions(actionTypes.visibilities, 'visibilities');
