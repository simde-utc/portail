import React from 'react'
import { connect } from 'react-redux'
import { Route, Redirect } from 'react-router-dom'

const PrivateRoute = ({ component: Component, redirect, authorized, isAuthenticated, ...params }) => (
	<Route
	  { ...params }
	  render={ props => (
	    (authorized || (authorized === undefined && isAuthenticated)) ? (
				<Component {...props} />
	    ) : (
				<Redirect to={{ pathname: redirect || '/', state: { from: props.location } }} />
			)
		)}
	/>
);

export default connect(store => ({
  isAuthenticated: store.isFetched('user'),
}))(PrivateRoute);
