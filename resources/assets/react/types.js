import { createActionTypes } from './utils';

export const userActionTypes = createActionTypes('USER');
export const assoActionTypes = createActionTypes('ASSO');

export default {
	user: userActionTypes,
	asso: assoActionTypes,
}