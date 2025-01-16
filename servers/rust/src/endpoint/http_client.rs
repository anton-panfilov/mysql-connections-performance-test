use axum::{extract::Query, http::header, http::Response, response::IntoResponse};
use reqwest::Client;
use serde::Deserialize;


#[derive(Deserialize)]
pub struct HttpClientQuery {
    link: String,
}

pub async fn axum_endpoint(Query(params): Query<HttpClientQuery>) -> impl IntoResponse {
    let client = Client::new();

    match client.get(&params.link).send().await {
        Ok(res) => {
            match res.text().await {
                Ok(body) => Response::builder()
                    .header(header::CONTENT_TYPE, "text/plain")
                    .body(body)
                    .unwrap(),
                Err(_) => Response::builder()
                    .status(500)
                    .header(header::CONTENT_TYPE, "text/plain")
                    .body("Failed to read response body".to_string())
                    .unwrap(),
            }
        }
        Err(_) => Response::builder()
            .status(500)
            .header(header::CONTENT_TYPE, "text/plain")
            .body("Failed to fetch the URL".to_string())
            .unwrap(),
    }
}