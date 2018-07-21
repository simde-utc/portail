import { createCrudTypes } from './utils';

// CRUD Action Types
export const usersActionTypes = createCrudTypes('USERS');
export const assosActionTypes = createCrudTypes('ASSOS');

export default {
	users: usersActionTypes,
	assos: assosActionTypes,
}