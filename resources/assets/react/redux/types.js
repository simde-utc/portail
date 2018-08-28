import { createCrudTypes } from './utils';

// CRUD Action Types
export const usersActionTypes = createCrudTypes('USERS');
export const assosActionTypes = createCrudTypes('ASSOS');
export const articlesActionTypes = createCrudTypes('ARTICLES');

export default {
	users: usersActionTypes,
	assos: assosActionTypes,
    articles: articlesActionTypes
}