import React from 'react'

class ErrorDebugger extends React.Component {
	render() {
		const { error, info } = this.props.details
		return <div>
			<h1>Oupss...</h1>
			<p>
				An error happened, check console logs to know more.
			</p>
		</div>
	}
}

export default class ErrorCatcher extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			hasError: false,
			error: null,
			info: null
		}
	}

	logError(error, info) {
		console.warn("Error catched !", error, info)
	}

	componentDidCatch(error, info) {
		this.setState({ hasError: true, error, info })
		this.logError(error, info)
	}

	render() {
		let { hasError, error, info } = this.state
		if (hasError)
			return <ErrorDebugger details={ error, info } />
		else
			return this.props.children
	}
}