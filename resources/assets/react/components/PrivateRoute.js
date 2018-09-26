import React from 'react'
import { Route, Redirect } from 'react-router-dom'

const PrivateRoute = ({ component: Component, redirect, authorized, ...params }) => (
	<Route
	  { ...params }
	  render={ props => (
	    authorized ? (
				<Component {...props} />
	    ) : (
				<Redirect to={{ pathname: redirect || '/', state: { from: props.location } }} />
			)
		)}
	/>
);

export default PrivateRoute;
