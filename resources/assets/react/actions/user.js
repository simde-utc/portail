export function fetchLogguedUser() {
	return {
		type: "FETCH_USER_FULFILLED",
		payload: {
			name: "Alex",
			age: 35
		}
	}
}

