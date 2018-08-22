import React from 'react'
import { Route, Redirect } from 'react-router-dom'
import store from '../redux/store'
import loggedUserActions from '../redux/custom/loggedUser/actions';

const PrivateRoute = (...params) => {
	const isAuthenticated = store.getState().loggedUser.isAuthenticated()
	if (isAuthenticated)
		return <Route {...params} />
	else
		return <Redirect to='/' />
}

export default PrivateRoute