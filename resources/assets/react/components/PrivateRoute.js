import React from 'react'
import { Route, Redirect } from 'react-router-dom'
import store from '../redux/store'
import actions from '../redux/actions';

const renderRouteOrReject = props => {
	return store.getData('user', false) ? (
		<Component { ...props } />
	) : (
		<Redirect to={{
			pathname: '/',
			state: { from: props.location }
		}} />
	)
}

const PrivateRoute = ({ component: Component, ...params}) => (<Route { ...params } render={ renderRouteOrReject } />)

export default PrivateRoute
