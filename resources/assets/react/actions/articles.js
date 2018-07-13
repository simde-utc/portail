export function fetchArticles() {
    return function(dispatch) {
        axios.get("/api/v1/articles")
            .then(response => {
                dispatch({
                    type: "FETCH_ARTICLES_FULFILLED",
                    payload: response.data
                })
            })
            .catch(err => {
                dispatch({
                    type: "FETCH_ARTICLES_ERROR",
                    payload: err
                })
            })
    }
}