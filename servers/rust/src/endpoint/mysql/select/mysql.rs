use mysql::prelude::*;
use mysql::*;
use std::sync::Arc;
use std::time::Instant;

use axum::{extract::Extension, Json};
use axum::extract::Query;
use serde::Deserialize;
use crate::scheme::response_select::ResponseSelect;

#[derive(Deserialize)]
pub struct QueryParams {
    s: Option<u32>,
}

pub async fn axum_endpoint(
    Query(params): Query<QueryParams>,
    Extension(pool): Extension<Arc<Pool>>
) -> Json<ResponseSelect> {
    let iterations = params.s.unwrap_or(20_000).clamp(1, 100_000);

    let columns: &str = "id";
    let first_id = 1;

    let mut conn = pool.get_conn().unwrap();

    let start = Instant::now();
    for i in first_id..first_id + iterations {
        //let _: Option<(u64, Vec<u8>, String, i8, i32, String, bool, serde_json::Value, Vec<u8>)> =
        let _: Option<u64> = conn
            .exec_first(format!(r"select {columns} from _sandbox where id={i}"), ())
            .unwrap();
    }
    let duration = start.elapsed().as_secs_f64();

    Json(ResponseSelect {
        driver: "mysql",
        method: "exec_first",
        data_size: iterations,
        columns: 1,
        duration,
        ..ResponseSelect::default()
    })
}
