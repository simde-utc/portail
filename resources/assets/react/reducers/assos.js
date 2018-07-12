export default function assosReducer(state = {
	assos: [],
	fetching: false,
	fetched: false,
	error: null,
}, action) {
	switch (action.type) {
		case 'FETCH_ASSOS_PENDING':
			return {
				...state,
				fetching: true
			};
			break;
		case 'FETCH_ASSOS_FULFILLED':
			return {
				...state,
				fetching: false,
				fetched: true,
				assos: action.payload.data
			}
			break;
		case 'FETCH_ASSOS_REJECTED':
			return {
				...state,
				fetching: false,
				error: action.payload
			}
			break;
	}
	return state;
}