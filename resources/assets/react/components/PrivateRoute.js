import React from 'react'
import { Route, Redirect } from 'react-router-dom'

const PrivateRoute = (...params) => {
	const isAuthenticated = false
	if (isAuthenticated)
		return <Route {...params} />
	else
		return <Redirect to='/login' />
}

export default PrivateRoute