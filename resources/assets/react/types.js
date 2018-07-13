import { createActionTypes } from './utils';

export const usersActionTypes = createActionTypes('USERS');
export const assosActionTypes = createActionTypes('ASSOS');
export const articlesActionTypes = createActionTypes('ARTICLES');

export default {
	users: usersActionTypes,
	assos: assosActionTypes,
    articles: articlesActionTypes,
}