export default function userReducer(state = {
	user: null,
	fetching: false,
	fetched: false,
	error: null,
	isConnected: () => {
		return this.user === null;
	}
}, action) {
	switch (action.type) {
		case 'FETCH_USER_PENDING':
			return {
				...state,
				fetching: true
			};
			break;
		case 'FETCH_USER_FULFILLED':
			return {
				...state,
				fetching: false,
				fetched: true,
				user: action.payload
			}
			break;
		case 'FETCH_USER_REJECTED':
			return {
				...state,
				fetching: false,
				error: action.payload
			}
			break;
	}
	return state;
}