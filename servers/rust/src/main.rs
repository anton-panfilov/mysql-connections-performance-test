use mysql::*;
use std::sync::Arc;

use axum::{extract::Extension, routing::get, Router};
use sqlx::mysql::MySqlPoolOptions;
use sqlx::MySql;

mod endpoint;
mod scheme;

#[tokio::main]
async fn main() {
    tracing_subscriber::fmt::init();
    let url = std::env::var("DATABASE_URL").unwrap();

    let mysql_pool = Pool::new(url.as_str()).unwrap();
    let mysql_pool_shared = Arc::new(mysql_pool);

    let sqlx_pool: sqlx::Pool<MySql> = MySqlPoolOptions::new()
        .max_connections(3)
        .connect(url.as_str()).await.unwrap();
    let sqlx_pool_shared = Arc::new(sqlx_pool);

    let app = Router::new()
        .route("/hello-world", get(endpoint::hello_world::axum_endpoint))
        .route("/migration", get(endpoint::mysql::migration::axum_endpoint))
        .route("/select/serial/mysql", get(endpoint::mysql::select::mysql::axum_endpoint))
        .route("/select/serial/sqlx", get(endpoint::mysql::select::sqlx::axum_endpoint))
        .layer(Extension(mysql_pool_shared))
        .layer(Extension(sqlx_pool_shared));

    let bind = std::env::var("APP_BIND").unwrap();
    let listener = tokio::net::TcpListener::bind(bind).await.unwrap();

    axum::serve(listener, app)
        .await.unwrap();
}