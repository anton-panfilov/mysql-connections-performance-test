use axum::{http::header, http::Response};
use axum::response::IntoResponse;

pub async fn axum_endpoint() -> impl IntoResponse {
    Response::builder()
        .header(header::CONTENT_TYPE, "text/plain")
        .body("Hello World".to_string())
        .unwrap()
}