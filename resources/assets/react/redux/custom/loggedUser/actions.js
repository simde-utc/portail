import loggedUserTypes from './types';

const updateUserPropActionCreator = (nodePath, uriPath) => ({
	type: loggedUserTypes.updateUserProp,
	meta: { arrayAction: 'replace', nodePath, timestamp: Date.now() },
	payload: axios.get(`/api/v1/user${uriPath}`)
})

const loggedUserActions = {
	// Get User properties
	getInfo: () => updateUserPropActionCreator('info', ''),
	getAuths: () => updateUserPropActionCreator('auths', '/auths'),
	getRoles: () => updateUserPropActionCreator('roles', '/roles'),
	getDetails: () => updateUserPropActionCreator('details', '/details'),
	getPreferences: () => updateUserPropActionCreator('preferences', '/preferences'),
	getCalendars: () => updateUserPropActionCreator('calendars', '/calendars'),

	// Remove all data about the user
	removeUser: () => ({
		type: loggedUserTypes.removeUser,
		payload: null
	}),
}

export default loggedUserActions;
