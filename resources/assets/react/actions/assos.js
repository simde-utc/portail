export const fetchAssos = () => ({
	type: "FETCH_ASSOS",
	payload: axios.get("/api/v1/assos?all")
})

export const fetchFakeAssos = () => ({
	type: "FETCH_ASSOS_FULFILLED",
	payload: {data : [
		{ id: 1, 	parent_id: null,	shortname: 'BDE', 		login: 'BDE'	},
		{ id: 2, 	parent_id: 1,		shortname: 'PAE', 		login: 'PAE'	},
		{ id: 3, 	parent_id: 1,		shortname: 'PSEC', 		login: 'PSEC'	},
		{ id: 4, 	parent_id: 1,		shortname: 'PTE', 		login: 'PTE'	},
		{ id: 5, 	parent_id: 1,		shortname: 'PVDC', 		login: 'PVDC'	},
		{ id: 6, 	parent_id: 2,		shortname: 'azd2', 		login: 'azd2'	},
		{ id: 7, 	parent_id: 2,		shortname: 'azd2', 		login: 'azd2'	},
		{ id: 8, 	parent_id: 2,		shortname: 'azd2', 		login: 'azd2'	},
		{ id: 9, 	parent_id: 3,		shortname: 'azd3', 		login: 'azd3'	},
		{ id: 10, 	parent_id: 3,		shortname: 'azd3', 		login: 'azd3'	},
		{ id: 11, 	parent_id: 4,		shortname: 'azd4', 		login: 'azd4'	},
		{ id: 12, 	parent_id: 5,		shortname: 'azd5', 		login: 'azd5'	},
		{ id: 13, 	parent_id: 7,		shortname: 'Ah bon', 	login: 'Ah bon'	},
		{ id: 14, 	parent_id: 13,		shortname: 'Okay', 		login: 'Okay'	},
		{ id: 15, 	parent_id: 14,		shortname: 'Okay', 		login: 'Okay'	},
		{ id: 16, 	parent_id: 15,		shortname: 'Okay', 		login: 'Okay'	},
	]}
})
