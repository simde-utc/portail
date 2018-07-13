import { createActionTypes } from './utils';

export const usersActionTypes = createActionTypes('USERS');
export const assosActionTypes = createActionTypes('ASSOS');

export default {
	users: usersActionTypes,
	assos: assosActionTypes,
}