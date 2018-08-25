import React from 'react'
import { Route, Redirect } from 'react-router-dom'
import store from '../redux/store'
import loggedUserActions from '../redux/custom/loggedUser/actions';


const renderRouteOrReject = props => {
	// TODO Fetch
	const isAuthenticated = store.getState().loggedUser.isAuthenticated()
	return isAuthenticated ? (
		<Component { ...props } />
	) : (
		<Redirect to={{
			pathname: '/',
			state: { from: props.location}
		}} />
	)
}

const PrivateRoute = ({ component: Component, ...params}) => (<Route { ...params } render={ renderRouteOrReject } />)

export default PrivateRoute
