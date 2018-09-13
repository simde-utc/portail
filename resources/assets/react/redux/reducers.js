import { combineReducers } from 'redux';
import { createCrudReducer } from './utils';
import crudActionTypes from './types';

const usersReducer = createCrudReducer(crudActionTypes.users)
const assosReducer = createCrudReducer(crudActionTypes.assos)
const assoMembersReducer = createCrudReducer(crudActionTypes.assoMembers)
const articlesReducer = createCrudReducer(crudActionTypes.articles)
const visibilitiesReducer = createCrudReducer(crudActionTypes.visibilities)
const calendarsReducer = createCrudReducer(crudActionTypes.calendars)
const calendarEventsReducer = createCrudReducer(crudActionTypes.calendarEvents)

// Custom Reducers
import loggedUserReducer from './custom/loggedUser/reducers';


// Combine
export default combineReducers({
	// CRUD
	users: usersReducer,
	assos: assosReducer,
	assoMembers: assoMembersReducer,
	articles: articlesReducer,
	loggedUser: loggedUserReducer,
	visibilities: visibilitiesReducer,
	calendars: calendarsReducer,
	calendarEvents: calendarEventsReducer,
});
