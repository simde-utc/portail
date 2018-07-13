export const fetchArticles = () => ({
    type: "FETCH_ARTICLES",
    payload: axios.get("/api/v1/articles")
})
