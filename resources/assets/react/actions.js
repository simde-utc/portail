import { createCrudActionSet } from './utils';
import actionTypes from './types';

export const usersActions = createCrudActionSet(actionTypes.users, 'users')
export const assosActions = createCrudActionSet(actionTypes.assos, 'assos')
export const articlesActions = createCrudActionSet(actionTypes.articles, 'articles')