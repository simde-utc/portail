export default function assoReducer(state = {
	asso: null,
	fetching: false,
	fetched: false,
	error: null,
}, action) {
	switch (action.type) {
		case 'FETCH_ASSO_PENDING':
			return {
				...state,
				fetching: true
			};
			break;
		case 'FETCH_ASSO_FULFILLED':
			return {
				...state,
				fetching: false,
				fetched: true,
				asso: action.payload.data
			}
			break;
		case 'FETCH_ASSO_REJECTED':
			return {
				...state,
				fetching: false,
				error: action.payload
			}
			break;
	}
	return state;
}