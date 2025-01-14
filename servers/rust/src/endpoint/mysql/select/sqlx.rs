use mysql::*;
use std::sync::Arc;
use std::time::Instant;

use crate::scheme::response_select::ResponseSelect;
use axum::{extract::Extension, Json};
use axum::extract::Query;
use chrono::{DateTime, Utc};
use serde::Deserialize;
use sqlx::MySql;

#[derive(Deserialize)]
pub struct QueryParams {
    s: Option<u32>,
}

pub async fn axum_endpoint(
    Query(params): Query<QueryParams>,
    Extension(pool): Extension<Arc<sqlx::Pool<MySql>>>
) -> Json<ResponseSelect> {
    let iterations = params.s.unwrap_or(20_000).clamp(1, 100_000);

    let columns: &str = "`id`, `guid`, `created`, `enum`, `int`, `string`, `bool`, `json`, `encrypted_json`";
    let first_id = 1;

    let start = Instant::now();
    for i in first_id..first_id + iterations {
        let _row: (u64, Vec<u8>, DateTime<Utc>, i8, i32, String, bool, serde_json::Value, Vec<u8>) = sqlx::query_as(
            format!("SELECT {columns} FROM _sandbox where id=? limit 1").as_str()
        )
            .bind(i)
            .fetch_one(&*pool)
            .await
            .unwrap();
    }
    let duration = start.elapsed().as_secs_f64();

    Json(
        ResponseSelect {
            driver: "sqlx.mysql",
            method: "query_as",
            data_size: iterations,
            duration,
            ..ResponseSelect::default()
        }
    )
}