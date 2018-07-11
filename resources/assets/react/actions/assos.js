export function fetchAssos() {
	return function(dispatch) {
		axios.get("/api/v1/assos")
			.then(response => {
				dispatch({
					type: "FETCH_ASSOS_FULFILLED",
					payload: response.data
				})
			})
			.catch(err => {
				dispatch({
					type: "FETCH_ASSO_ERROR",
					payload: err
				})
			})
	}
}