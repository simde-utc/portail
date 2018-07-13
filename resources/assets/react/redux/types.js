import { createCrudTypes } from './utils';

export const usersActionTypes = createCrudTypes('USERS');
export const assosActionTypes = createCrudTypes('ASSOS');

export default {
	users: usersActionTypes,
	assos: assosActionTypes,
}