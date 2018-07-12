export const fetchAssos = () => ({
	type: "FETCH_ASSOS",
	payload: axios.get("/api/v1/assos")
})
