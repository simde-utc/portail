export default function articlesReducer(state = {
    articles: [],
    fetching: false,
    fetched: false,
    error: null,
}, action) {
    switch (action.type) {
        case 'FETCH_ARTICLES_PENDING':
            return {
                ...state,
                fetching: true
            };
            break;
        case 'FETCH_ARTICLES_FULFILLED':
            return {
                ...state,
                fetching: false,
                fetched: true,
                articles: action.payload
            }
            break;
        case 'FETCH_ARTICLES_REJECTED':
            return {
                ...state,
                fetching: false,
                error: action.payload
            }
            break;
    }
    return state;
}