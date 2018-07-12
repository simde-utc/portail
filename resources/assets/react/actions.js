import { createCrudActionSet } from './utils';
import actionTypes from './types';

export const userActions = createCrudActionSet(actionTypes.user, 'users')
export const assoActions = createCrudActionSet(actionTypes.asso, 'assos')